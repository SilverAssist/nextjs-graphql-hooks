<?php
/**
 * Admin Panel class for NextJS GraphQL Hooks
 *
 * Handles Settings Hub integration and admin interface
 *
 * @package NextJSGraphQLHooks
 * @since 1.0.4
 * @author Silver Assist
 * @version 1.1.0
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
					"tab_title" => \__("GraphQL Hooks", "nextjs-graphql-hooks"),
					"capability" => "manage_options",
					"plugin_file" => NEXTJS_GRAPHQL_HOOKS_PLUGIN_FILE,
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
		$actions = [];

		$main = NextJS_GraphQL_Hooks::get_instance();
		if ($main->get_updater()) {
			$actions[] = [
				"id" => "check_updates",
				"label" => \__("Check Updates", "nextjs-graphql-hooks"),
				"callback" => [$this, "render_update_check_script"],
				"class" => "button"
			];
		}

		return $actions;
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

			<div class="nextjs-graphql-hooks-content">

				<!-- GraphQL Queries -->
				<div class="status-card">
					<div class="card-header">
						<span class="dashicons dashicons-editor-code"></span>
						<h3><?php echo \esc_html__("Available GraphQL Queries", "nextjs-graphql-hooks"); ?></h3>
					</div>
					<div class="card-content">
						<?php $this->render_queries_section(); ?>
					</div>
				</div>

				<!-- Filter System -->
				<div class="status-card">
					<div class="card-header">
						<span class="dashicons dashicons-admin-generic"></span>
						<h3><?php echo \esc_html__("Extensibility", "nextjs-graphql-hooks"); ?></h3>
					</div>
					<div class="card-content">
						<?php $this->render_filters_section(); ?>
					</div>
				</div>

				<!-- Documentation -->
				<div class="status-card">
					<div class="card-header">
						<span class="dashicons dashicons-media-document"></span>
						<h3><?php echo \esc_html__("Documentation", "nextjs-graphql-hooks"); ?></h3>
					</div>
					<div class="card-content">
						<?php $this->render_documentation_section(); ?>
					</div>
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
				<th scope="row"><?php echo \esc_html__("WPGraphQL", "nextjs-graphql-hooks"); ?></th>
				<td>
					<?php if ($wpgraphql_active): ?>
						<span class="status-indicator active">
							<?php echo \esc_html__("Active", "nextjs-graphql-hooks"); ?>
						</span>
					<?php else: ?>
						<span class="status-indicator inactive">
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
						<span class="status-indicator active">
							<?php echo \esc_html__("Active", "nextjs-graphql-hooks"); ?>
						</span>
						<p class="description">
							<?php echo \esc_html__("Elementor content fields are available", "nextjs-graphql-hooks"); ?>
						</p>
					<?php else: ?>
						<span class="status-indicator inactive">
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
	 *
	 * Delegates to wp-github-updater's built-in enqueueCheckUpdatesScript() which
	 * provides centralized JS, AJAX handling, admin notices, and auto-redirect.
	 *
	 * @since 1.0.5
	 * @param string $plugin_slug Plugin slug (passed by Settings Hub)
	 * @return void
	 */
	public function render_update_check_script(string $plugin_slug = ""): void
	{
		$main = NextJS_GraphQL_Hooks::get_instance();
		$updater = $main->get_updater();

		if (!$updater) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inline JavaScript from wp-github-updater
		echo $updater->enqueueCheckUpdatesScript();
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
