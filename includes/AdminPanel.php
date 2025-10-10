<?php
/**
 * Admin Panel class for NextJS GraphQL Hooks
 *
 * Handles Settings Hub integration and admin interface
 *
 * @package NextJSGraphQLHooks
 * @since 1.0.4
 * @author Silver Assist
 * @version 1.0.4
 * @license Polyform Noncommercial License 1.0.0
 */

namespace NextJSGraphQLHooks;

use SilverAssist\SettingsHub\SettingsHub;

// Prevent direct access
defined("ABSPATH") or exit;

/**
 * Class AdminPanel
 *
 * Manages admin interface and Settings Hub integration
 */
class AdminPanel
{
	private static ?AdminPanel $instance = null;

	/**
	 * Get the singleton instance
	 *
	 * @since 1.0.4
	 * @return AdminPanel
	 */
	public static function get_instance(): AdminPanel
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * AdminPanel constructor
	 *
	 * @since 1.0.4
	 */
	private function __construct()
	{
		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks
	 *
	 * @since 1.0.4
	 * @return void
	 */
	private function init_hooks(): void
	{
		// CRITICAL: Use priority 4 to register before Settings Hub (priority 5)
		\add_action("admin_menu", [$this, "register_with_hub"], 4);
		\add_action("admin_enqueue_scripts", [$this, "enqueue_admin_scripts"]);
		\add_action("wp_ajax_nextjs_graphql_hooks_check_updates", [$this, "ajax_check_updates"]);
	}

	/**
	 * Register plugin with Settings Hub
	 *
	 * @since 1.0.4
	 * @return void
	 */
	public function register_with_hub(): void
	{
		// Check if Settings Hub is available
		if (!\class_exists(SettingsHub::class)) {
			// Fallback: Register standalone menu
			$this->add_standalone_menu();
			return;
		}

		try {
			// Get Settings Hub instance
			$hub = SettingsHub::get_instance();

			// Register plugin
			$hub->register_plugin(
				"nextjs-graphql-hooks",
				\__("NextJS GraphQL Hooks", "nextjs-graphql-hooks"),
				[$this, "render_admin_page"],
				[
					"description" => \__("GraphQL hooks for NextJS integration with Elementor support", "nextjs-graphql-hooks"),
					"version" => NEXTJS_GRAPHQL_HOOKS_VERSION,
					"tab_title" => \__("Settings", "nextjs-graphql-hooks"),
					"capability" => "manage_options",
					"actions" => $this->get_hub_actions()
				]
			);
		} catch (\Exception $e) {
			\error_log("NextJS GraphQL Hooks - Settings Hub registration failed: " . $e->getMessage());
			$this->add_standalone_menu();
		}
	}

	/**
	 * Fallback: Register standalone menu when Settings Hub unavailable
	 *
	 * @since 1.0.4
	 * @return void
	 */
	private function add_standalone_menu(): void
	{
		\add_options_page(
			\__("NextJS GraphQL Hooks", "nextjs-graphql-hooks"),
			\__("NextJS GraphQL", "nextjs-graphql-hooks"),
			"manage_options",
			"nextjs-graphql-hooks",
			[$this, "render_admin_page"]
		);
	}

	/**
	 * Define action buttons for Settings Hub
	 *
	 * @since 1.0.4
	 * @return array
	 */
	private function get_hub_actions(): array
	{
		return [
			[
				"id" => "check_updates",
				"label" => \__("Check Updates", "nextjs-graphql-hooks"),
				"callback" => [$this, "render_update_check_script"],
				"class" => "button button-secondary"
			]
		];
	}

	/**
	 * Render admin page content
	 *
	 * @since 1.0.4
	 * @return void
	 */
	public function render_admin_page(): void
	{
		// Verify user capability
		if (!\current_user_can("manage_options")) {
			\wp_die(\esc_html__("Sorry, you are not allowed to access this page.", "nextjs-graphql-hooks"));
		}

		// Display admin page
		?>
		<div class="wrap nextjs-graphql-hooks-admin">
			<h1><?php echo \esc_html__("NextJS GraphQL Hooks", "nextjs-graphql-hooks"); ?></h1>

			<div class="nextjs-graphql-hooks-content">
				<!-- Plugin Status -->
				<div class="card">
					<h2><?php echo \esc_html__("Plugin Status", "nextjs-graphql-hooks"); ?></h2>
					<?php $this->render_status_section(); ?>
				</div>

				<!-- GraphQL Queries -->
				<div class="card">
					<h2><?php echo \esc_html__("Available GraphQL Queries", "nextjs-graphql-hooks"); ?></h2>
					<?php $this->render_queries_section(); ?>
				</div>

				<!-- Filter System -->
				<div class="card">
					<h2><?php echo \esc_html__("Extensibility", "nextjs-graphql-hooks"); ?></h2>
					<?php $this->render_filters_section(); ?>
				</div>

				<!-- Documentation -->
				<div class="card">
					<h2><?php echo \esc_html__("Documentation", "nextjs-graphql-hooks"); ?></h2>
					<?php $this->render_documentation_section(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render plugin status section
	 *
	 * @since 1.0.4
	 * @return void
	 */
	private function render_status_section(): void
	{
		$wpgraphql_active = \class_exists("WPGraphQL");
		$elementor_active = \class_exists("\Elementor\Plugin");

		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php echo \esc_html__("Plugin Version", "nextjs-graphql-hooks"); ?></th>
				<td><code><?php echo \esc_html(NEXTJS_GRAPHQL_HOOKS_VERSION); ?></code></td>
			</tr>
			<tr>
				<th scope="row"><?php echo \esc_html__("WPGraphQL", "nextjs-graphql-hooks"); ?></th>
				<td>
					<?php if ($wpgraphql_active): ?>
						<span class="status-badge status-active">
							<span class="dashicons dashicons-yes"></span>
							<?php echo \esc_html__("Active", "nextjs-graphql-hooks"); ?>
						</span>
					<?php else: ?>
						<span class="status-badge status-inactive">
							<span class="dashicons dashicons-no"></span>
							<?php echo \esc_html__("Inactive", "nextjs-graphql-hooks"); ?>
						</span>
						<p class="description">
							<?php
							echo \sprintf(
								\esc_html__("WPGraphQL is required for this plugin to work. %sInstall WPGraphQL%s", "nextjs-graphql-hooks"),
								'<a href="' . \esc_url(\admin_url("plugin-install.php?s=wpgraphql&tab=search&type=term")) . '">',
								'</a>'
							);
							?>
						</p>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php echo \esc_html__("Elementor", "nextjs-graphql-hooks"); ?></th>
				<td>
					<?php if ($elementor_active): ?>
						<span class="status-badge status-active">
							<span class="dashicons dashicons-yes"></span>
							<?php echo \esc_html__("Active", "nextjs-graphql-hooks"); ?>
						</span>
						<p class="description">
							<?php echo \esc_html__("Elementor content fields are available", "nextjs-graphql-hooks"); ?>
						</p>
					<?php else: ?>
						<span class="status-badge status-inactive">
							<span class="dashicons dashicons-no"></span>
							<?php echo \esc_html__("Inactive", "nextjs-graphql-hooks"); ?>
						</span>
						<p class="description">
							<?php echo \esc_html__("Elementor is optional. Install it to enable Elementor-specific GraphQL fields.", "nextjs-graphql-hooks"); ?>
						</p>
					<?php endif; ?>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render GraphQL queries section
	 *
	 * @since 1.0.4
	 * @return void
	 */
	private function render_queries_section(): void
	{
		?>
		<h3><?php echo \esc_html__("Page Fields", "nextjs-graphql-hooks"); ?></h3>
		<ul class="query-list">
			<li>
				<code>elementorContent</code>
				<span class="query-description"><?php echo \esc_html__("Returns Elementor page content (HTML)", "nextjs-graphql-hooks"); ?></span>
			</li>
			<li>
				<code>elementorCSSFile</code>
				<span class="query-description"><?php echo \esc_html__("Returns Elementor CSS file URL", "nextjs-graphql-hooks"); ?></span>
			</li>
		</ul>

		<h3><?php echo \esc_html__("Root Queries", "nextjs-graphql-hooks"); ?></h3>
		<ul class="query-list">
			<li>
				<code>elementorLibraryKit</code>
				<span class="query-description"><?php echo \esc_html__("Returns Elementor library kit information (kit_id, css_file)", "nextjs-graphql-hooks"); ?></span>
			</li>
		</ul>

		<div class="query-example">
			<h4><?php echo \esc_html__("Example Query", "nextjs-graphql-hooks"); ?></h4>
			<pre><code>query GetPage($id: ID!) {
  page(id: $id) {
    elementorContent
    elementorCSSFile
  }
  elementorLibraryKit {
    kit_id
    css_file
  }
}</code></pre>
		</div>
		<?php
	}

	/**
	 * Render filter system section
	 *
	 * @since 1.0.4
	 * @return void
	 */
	private function render_filters_section(): void
	{
		?>
		<p><?php echo \esc_html__("This plugin provides three filter hooks for custom extensions:", "nextjs-graphql-hooks"); ?></p>
		
		<ul class="filter-list">
			<li>
				<code>nextjs_graphql_hooks_register_types</code>
				<span class="filter-description"><?php echo \esc_html__("Register custom GraphQL object types", "nextjs-graphql-hooks"); ?></span>
			</li>
			<li>
				<code>nextjs_graphql_hooks_register_queries</code>
				<span class="filter-description"><?php echo \esc_html__("Register custom root query fields", "nextjs-graphql-hooks"); ?></span>
			</li>
			<li>
				<code>nextjs_graphql_hooks_register_fields</code>
				<span class="filter-description"><?php echo \esc_html__("Register custom fields on existing types", "nextjs-graphql-hooks"); ?></span>
			</li>
		</ul>

		<div class="filter-example">
			<h4><?php echo \esc_html__("Example: Register Custom Field", "nextjs-graphql-hooks"); ?></h4>
			<pre><code>add_filter('nextjs_graphql_hooks_register_fields', function ($fields) {
    $fields['Post'] = [
        'customMeta' => [
            'type' => 'String',
            'description' => __('Custom meta field', 'textdomain'),
            'resolve' => function ($post) {
                return get_post_meta($post->databaseId, 'custom_key', true);
            }
        ]
    ];
    return $fields;
});</code></pre>
		</div>
		<?php
	}

	/**
	 * Render documentation section
	 *
	 * @since 1.0.4
	 * @return void
	 */
	private function render_documentation_section(): void
	{
		?>
		<p><?php echo \esc_html__("For complete documentation and examples, visit:", "nextjs-graphql-hooks"); ?></p>
		<ul class="documentation-links">
			<li>
				<a href="https://github.com/SilverAssist/nextjs-graphql-hooks" target="_blank" rel="noopener noreferrer">
					<span class="dashicons dashicons-admin-site"></span>
					<?php echo \esc_html__("GitHub Repository", "nextjs-graphql-hooks"); ?>
				</a>
			</li>
			<li>
				<a href="https://github.com/SilverAssist/nextjs-graphql-hooks/blob/main/README.md" target="_blank" rel="noopener noreferrer">
					<span class="dashicons dashicons-media-document"></span>
					<?php echo \esc_html__("Plugin Documentation", "nextjs-graphql-hooks"); ?>
				</a>
			</li>
			<li>
				<a href="https://www.wpgraphql.com/docs" target="_blank" rel="noopener noreferrer">
					<span class="dashicons dashicons-editor-help"></span>
					<?php echo \esc_html__("WPGraphQL Documentation", "nextjs-graphql-hooks"); ?>
				</a>
			</li>
		</ul>
		<?php
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @since 1.0.4
	 * @param string $hook_suffix Current admin page hook suffix
	 * @return void
	 */
	public function enqueue_admin_scripts(string $hook_suffix): void
	{
		// Define allowed hook suffixes for multiple contexts
		$allowed_hooks = [
			"settings_page_nextjs-graphql-hooks",        // Standalone fallback
			"silver-assist_page_nextjs-graphql-hooks",   // Settings Hub submenu
			"toplevel_page_nextjs-graphql-hooks"         // Direct top-level (if applicable)
		];

		// Only load on plugin pages
		if (!\in_array($hook_suffix, $allowed_hooks, true)) {
			return;
		}

		// Enqueue CSS
		\wp_enqueue_style(
			"nextjs-graphql-hooks-admin",
			NEXTJS_GRAPHQL_HOOKS_PLUGIN_URL . "assets/css/admin.css",
			[],
			NEXTJS_GRAPHQL_HOOKS_VERSION
		);

		// Enqueue JavaScript
		\wp_enqueue_script(
			"nextjs-graphql-hooks-admin",
			NEXTJS_GRAPHQL_HOOKS_PLUGIN_URL . "assets/js/admin.js",
			["jquery"],
			NEXTJS_GRAPHQL_HOOKS_VERSION,
			true
		);

		// Localize script with AJAX data
		\wp_localize_script("nextjs-graphql-hooks-admin", "nextjsGraphQLHooksData", [
			"ajaxurl" => \admin_url("admin-ajax.php"),
			"nonce" => \wp_create_nonce("nextjs_graphql_hooks_nonce"),
			"strings" => [
				"saved" => \__("Settings saved!", "nextjs-graphql-hooks"),
				"error" => \__("An error occurred", "nextjs-graphql-hooks")
			]
		]);
	}

	/**
	 * Render update check button script
	 * ⚠️ CRITICAL: Must echo JavaScript, not return it
	 *
	 * @since 1.0.4
	 * @param string $plugin_slug Plugin slug (passed by Settings Hub)
	 * @return void
	 */
	public function render_update_check_script(string $plugin_slug = ""): void
	{
		// Enqueue update check script
		\wp_enqueue_script(
			"nextjs-graphql-hooks-update-check",
			NEXTJS_GRAPHQL_HOOKS_PLUGIN_URL . "assets/js/update-check.js",
			["jquery"],
			NEXTJS_GRAPHQL_HOOKS_VERSION,
			true
		);

		// Localize with update data
		\wp_localize_script("nextjs-graphql-hooks-update-check", "nextjsGraphQLHooksUpdateData", [
			"ajaxurl" => \admin_url("admin-ajax.php"),
			"nonce" => \wp_create_nonce("nextjs_graphql_hooks_check_updates"),
			"updateUrl" => \admin_url("plugins.php"),
			"strings" => [
				"checking" => \__("Checking for updates...", "nextjs-graphql-hooks"),
				"available" => \__("Update available! Redirecting...", "nextjs-graphql-hooks"),
				"upToDate" => \__("Plugin is up to date!", "nextjs-graphql-hooks"),
				"error" => \__("Error checking for updates", "nextjs-graphql-hooks")
			]
		]);

		// ⚠️ CRITICAL: Echo JavaScript code (Settings Hub expects echo, not return)
		echo "nextjsGraphQLHooksCheckUpdates(); return false;";
	}

	/**
	 * AJAX handler for update checking
	 *
	 * @since 1.0.4
	 * @return void
	 */
	public function ajax_check_updates(): void
	{
		// Verify nonce
		if (!\check_ajax_referer("nextjs_graphql_hooks_check_updates", "nonce", false)) {
			\wp_send_json_error([
				"message" => \__("Security check failed", "nextjs-graphql-hooks")
			]);
			return;
		}

		// Verify user capability
		if (!\current_user_can("manage_options")) {
			\wp_send_json_error([
				"message" => \__("Insufficient permissions", "nextjs-graphql-hooks")
			]);
			return;
		}

		try {
			// Force update check
			\wp_clean_plugins_cache();
			\wp_update_plugins();

			// Get plugin update information
			$update_plugins = \get_site_transient("update_plugins");
			$plugin_file = plugin_basename(NEXTJS_GRAPHQL_HOOKS_PLUGIN_FILE);

			// Check if update is available
			$update_available = isset($update_plugins->response[$plugin_file]);

			if ($update_available) {
				$update_data = $update_plugins->response[$plugin_file];
				\wp_send_json_success([
					"update_available" => true,
					"new_version" => $update_data->new_version ?? "unknown",
					"message" => \sprintf(
						\__("New version %s available!", "nextjs-graphql-hooks"),
						$update_data->new_version ?? ""
					)
				]);
			} else {
				\wp_send_json_success([
					"update_available" => false,
					"message" => \__("Plugin is up to date", "nextjs-graphql-hooks")
				]);
			}
		} catch (\Exception $e) {
			\error_log("NextJS GraphQL Hooks - Update check error: " . $e->getMessage());
			\wp_send_json_error([
				"message" => \__("Error checking for updates", "nextjs-graphql-hooks")
			]);
		}
	}

	/**
	 * Prevent cloning
	 *
	 * @since 1.0.4
	 * @return void
	 */
	private function __clone()
	{
	}

	/**
	 * Prevent unserialization
	 *
	 * @since 1.0.4
	 * @throws \Exception
	 * @return void
	 */
	public function __wakeup()
	{
		throw new \Exception("Cannot unserialize singleton");
	}
}
