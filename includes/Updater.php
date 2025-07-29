<?php
/**
 * NextJS GraphQL Hooks Updater - Custom GitHub Updates Handler
 *
 * Handles automatic updates from public GitHub releases for the NextJS GraphQL Hooks Plugin.
 * Provides seamless WordPress admin updates without requiring authentication tokens.
 *
 * @package NextJSGraphQLHooks
 * @since 1.0.0
 * @author Silver Assist
 * @version 1.0.0
 * @license GPL v2 or later
 */

namespace NextJSGraphQLHooks;

// Prevent direct access
defined("ABSPATH") or exit;

/**
 * Class Updater
 *
 * Handles automatic plugin updates from GitHub releases.
 */
class Updater
{
  /**
   * Plugin file path
   * @var string
   */
  private string $plugin_file;

  /**
   * Plugin slug (folder/file.php)
   * @var string
   */
  private string $plugin_slug;

  /**
   * Plugin basename (folder name only)
   * @var string
   */
  private string $plugin_basename;

  /**
   * GitHub repository (owner/repo)
   * @var string
   */
  private string $github_repo;

  /**
   * Current plugin version
   * @var string
   */
  private string $current_version;

  /**
   * Plugin data from header
   * @var array
   */
  private array $plugin_data;

  /**
   * Transient name for version cache
   * @var string
   */
  private string $version_transient;

  /**
   * Initialize the updater
   *
   * @param string $plugin_file Full path to main plugin file
   * @param string $github_repo GitHub repository in format "owner/repo"
   */
  public function __construct(string $plugin_file, string $github_repo)
  {
    $this->plugin_file = $plugin_file;
    $this->plugin_slug = plugin_basename($plugin_file);
    $this->plugin_basename = dirname($this->plugin_slug);
    $this->github_repo = $github_repo;
    $this->version_transient = "{$this->plugin_basename}_version_check";

    // Get plugin data
    if (!function_exists("get_plugin_data")) {
      require_once ABSPATH . "wp-admin/includes/plugin.php";
    }
    $this->plugin_data = \get_plugin_data($plugin_file);
    $this->current_version = $this->plugin_data["Version"];

    $this->init_hooks();
  }

  /**
   * Initialize WordPress hooks
   */
  private function init_hooks(): void
  {
    \add_filter("pre_set_site_transient_update_plugins", [$this, "check_for_update"]);
    \add_filter("plugins_api", [$this, "plugin_info"], 20, 3);
    \add_action("upgrader_process_complete", [$this, "clear_version_cache"], 10, 2);

    // Add custom action for manual version check
    \add_action("wp_ajax_nextjs_graphql_hooks_check_version", [$this, "manual_version_check"]);

    // Add settings page for updates
    \add_action("admin_menu", [$this, "add_settings_page"]);
    \add_action("admin_init", [$this, "register_settings"]);
  }

  /**
   * Check for plugin updates
   *
   * @param mixed $transient The update_plugins transient
   * @return mixed
   */
  public function check_for_update($transient)
  {
    if (empty($transient->checked)) {
      return $transient;
    }

    // Get latest version from GitHub
    $latest_version = $this->get_latest_version();

    if ($latest_version && version_compare($this->current_version, $latest_version, "<")) {
      $transient->response[$this->plugin_slug] = (object) [
        "slug" => $this->plugin_basename,
        "plugin" => $this->plugin_slug,
        "new_version" => $latest_version,
        "url" => "https://github.com/{$this->github_repo}",
        "package" => $this->get_download_url($latest_version),
        "tested" => get_bloginfo("version"),
        "requires_php" => "8.0",
        "compatibility" => new \stdClass(),
      ];
    }

    return $transient;
  }

  /**
   * Get plugin information for the update API
   *
   * @param false|object|array $result The result object or array
   * @param string $action The type of information being requested
   * @param object $args Plugin API arguments
   * @return false|object|array
   */
  public function plugin_info($result, string $action, object $args)
  {
    if ($action !== "plugin_information" || $args->slug !== $this->plugin_basename) {
      return $result;
    }

    $latest_version = $this->get_latest_version();
    $changelog = $this->get_changelog();

    return (object) [
      "slug" => $this->plugin_basename,
      "plugin" => $this->plugin_slug,
      "version" => $latest_version ?: $this->current_version,
      "author" => $this->plugin_data["Author"],
      "author_profile" => "https://github.com/SilverAssist",
      "requires" => "6.5",
      "tested" => get_bloginfo("version"),
      "requires_php" => "8.0",
      "name" => $this->plugin_data["Name"],
      "homepage" => "https://github.com/{$this->github_repo}",
      "sections" => [
        "description" => $this->plugin_data["Description"],
        "changelog" => $changelog,
        "installation" => $this->get_installation_instructions(),
      ],
      "download_link" => $this->get_download_url($latest_version),
      "last_updated" => $this->get_last_updated(),
    ];
  }

  /**
   * Get the latest version from GitHub releases
   *
   * @return string|false
   */
  private function get_latest_version()
  {
    // Check cache first
    $cached_version = \get_transient($this->version_transient);
    if ($cached_version !== false) {
      return $cached_version;
    }

    $api_url = "https://api.github.com/repos/{$this->github_repo}/releases/latest";
    $response = \wp_remote_get($api_url, [
      "timeout" => 15,
      "headers" => [
        "Accept" => "application/vnd.github.v3+json",
        "User-Agent" => "WordPress/" . get_bloginfo("version"),
      ],
    ]);

    if (\is_wp_error($response) || 200 !== \wp_remote_retrieve_response_code($response)) {
      \error_log("NextJS GraphQL Hooks Updater: Failed to fetch latest version");
      return false;
    }

    $body = \wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!isset($data["tag_name"])) {
      return false;
    }

    $version = ltrim($data["tag_name"], "v");

    // Cache for 12 hours
    \set_transient($this->version_transient, $version, 12 * HOUR_IN_SECONDS);

    return $version;
  }

  /**
   * Get download URL for a specific version
   *
   * @param string $version The version to download
   * @return string
   */
  private function get_download_url(string $version): string
  {
    return "https://github.com/{$this->github_repo}/releases/download/v{$version}/nextjs-graphql-hooks-v{$version}.zip";
  }

  /**
   * Get changelog from GitHub releases
   *
   * @return string
   */
  private function get_changelog(): string
  {
    $api_url = "https://api.github.com/repos/{$this->github_repo}/releases";
    $response = \wp_remote_get($api_url, [
      "timeout" => 15,
      "headers" => [
        "Accept" => "application/vnd.github.v3+json",
        "User-Agent" => "WordPress/" . get_bloginfo("version"),
      ],
    ]);

    if (\is_wp_error($response) || 200 !== \wp_remote_retrieve_response_code($response)) {
      return "Unable to fetch changelog. Visit the <a href=\"https://github.com/{$this->github_repo}/releases\">GitHub releases page</a> for updates.";
    }

    $body = \wp_remote_retrieve_body($response);
    $releases = json_decode($body, true);

    if (!is_array($releases)) {
      return "Unable to parse changelog.";
    }

    $changelog = "";
    foreach (array_slice($releases, 0, 5) as $release) { // Show last 5 releases
      $version = ltrim($release["tag_name"], "v");
      $date = date("Y-m-d", strtotime($release["published_at"]));
      $body = $release["body"] ?: "No release notes provided.";

      $changelog .= "<h4>Version {$version} ({$date})</h4>\n";
      $changelog .= "<div>" . wp_kses_post($body) . "</div>\n\n";
    }

    return $changelog ?: "No changelog available.";
  }

  /**
   * Get installation instructions
   *
   * @return string
   */
  private function get_installation_instructions(): string
  {
    return "
        <h4>Automatic Installation</h4>
        <ol>
            <li>Go to WordPress Admin → Plugins → Add New</li>
            <li>Search for 'NextJS GraphQL Hooks'</li>
            <li>Click 'Install Now' and then 'Activate'</li>
        </ol>
        
        <h4>Manual Installation</h4>
        <ol>
            <li>Download the plugin ZIP file</li>
            <li>Go to WordPress Admin → Plugins → Add New → Upload Plugin</li>
            <li>Choose the downloaded ZIP file and click 'Install Now'</li>
            <li>Activate the plugin</li>
        </ol>
        
        <h4>Requirements</h4>
        <ul>
            <li>WordPress 6.5 or higher</li>
            <li>PHP 8.0 or higher</li>
            <li>WPGraphQL plugin (automatically managed as dependency)</li>
            <li>Elementor (optional, for Elementor features)</li>
        </ul>
        ";
  }

  /**
   * Get last updated date
   *
   * @return string
   */
  private function get_last_updated(): string
  {
    $api_url = "https://api.github.com/repos/{$this->github_repo}/releases/latest";
    $response = \wp_remote_get($api_url, [
      "timeout" => 15,
      "headers" => [
        "Accept" => "application/vnd.github.v3+json",
        "User-Agent" => "WordPress/" . get_bloginfo("version"),
      ],
    ]);

    if (\is_wp_error($response) || 200 !== \wp_remote_retrieve_response_code($response)) {
      return date("Y-m-d");
    }

    $body = \wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!isset($data["published_at"])) {
      return date("Y-m-d");
    }

    return date("Y-m-d", strtotime($data["published_at"]));
  }

  /**
   * Clear version cache after update
   *
   * @param \WP_Upgrader $upgrader WP_Upgrader instance
   * @param array $data Array of update data
   */
  public function clear_version_cache(\WP_Upgrader $upgrader, array $data): void
  {
    if ($data["action"] === "update" && $data["type"] === "plugin") {
      if (isset($data["plugins"]) && in_array($this->plugin_slug, $data["plugins"])) {
        \delete_transient($this->version_transient);
      }
    }
  }

  /**
   * Manual version check via AJAX
   */
  public function manual_version_check(): void
  {
    if (!current_user_can("update_plugins")) {
      \wp_die("Insufficient permissions");
    }

    \delete_transient($this->version_transient);
    $latest_version = $this->get_latest_version();

    \wp_send_json_success([
      "current_version" => $this->current_version,
      "latest_version" => $latest_version,
      "update_available" => version_compare($this->current_version, $latest_version, "<"),
    ]);
  }

  /**
   * Add settings page for updates
   */
  public function add_settings_page(): void
  {
    \add_options_page(
      "NextJS GraphQL Hooks Updates",
      "GraphQL Hooks Updates",
      "manage_options",
      "nextjs-graphql-hooks-updates",
      [$this, "settings_page"]
    );
  }

  /**
   * Register settings
   */
  public function register_settings(): void
  {
    \register_setting("nextjs_graphql_hooks_updates", "nextjs_graphql_hooks_auto_updates");
  }

  /**
   * Settings page content
   */
  public function settings_page(): void
  {
    $latest_version = $this->get_latest_version();
    $update_available = version_compare($this->current_version, $latest_version, "<");
    ?>
    <div class="wrap">
      <h1><?php echo \esc_html(\__("NextJS GraphQL Hooks Updates", "nextjs-graphql-hooks")); ?></h1>

      <div class="card">
        <h2><?php echo \esc_html(\__("Version Information", "nextjs-graphql-hooks")); ?></h2>
        <table class="form-table">
          <tr>
            <th scope="row"><?php echo \esc_html(\__("Current Version", "nextjs-graphql-hooks")); ?></th>
            <td><?php echo \esc_html($this->current_version); ?></td>
          </tr>
          <tr>
            <th scope="row"><?php echo \esc_html(\__("Latest Version", "nextjs-graphql-hooks")); ?></th>
            <td>
              <?php echo \esc_html($latest_version ?: "Unknown"); ?>
              <?php if ($update_available): ?>
                <span class="dashicons dashicons-update" style="color: #d63638;"></span>
                <strong
                  style="color: #d63638;"><?php echo \esc_html(\__("Update Available!", "nextjs-graphql-hooks")); ?></strong>
              <?php else: ?>
                <span class="dashicons dashicons-yes-alt" style="color: #00a32a;"></span>
                <span style="color: #00a32a;"><?php echo \esc_html(\__("Up to date", "nextjs-graphql-hooks")); ?></span>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo \esc_html(\__("Repository", "nextjs-graphql-hooks")); ?></th>
            <td>
              <a href="https://github.com/<?php echo \esc_attr($this->github_repo); ?>" target="_blank">
                <?php echo \esc_html($this->github_repo); ?>
              </a>
            </td>
          </tr>
        </table>

        <p class="submit">
          <button type="button" class="button button-secondary" onclick="checkVersion()">
            <?php echo \esc_html(\__("Check for Updates", "nextjs-graphql-hooks")); ?>
          </button>
          <?php if ($update_available): ?>
            <a href="<?php echo \esc_url(\admin_url("update-core.php")); ?>" class="button button-primary">
              <?php echo \esc_html(\__("Go to Updates", "nextjs-graphql-hooks")); ?>
            </a>
          <?php endif; ?>
        </p>
      </div>
    </div>

    <script>
      function checkVersion() {
        const button = event.target;
        button.textContent = '<?php echo \esc_js(\__("Checking...", "nextjs-graphql-hooks")); ?>';
        button.disabled = true;

        fetch(ajaxurl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            action: 'nextjs_graphql_hooks_check_version'
          })
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              location.reload();
            } else {
              alert('<?php echo \esc_js(\__("Failed to check for updates", "nextjs-graphql-hooks")); ?>');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('<?php echo \esc_js(\__("Failed to check for updates", "nextjs-graphql-hooks")); ?>');
          })
          .finally(() => {
            button.textContent = '<?php echo \esc_js(\__("Check for Updates", "nextjs-graphql-hooks")); ?>';
            button.disabled = false;
          });
      }
    </script>
    <?php
  }
}
