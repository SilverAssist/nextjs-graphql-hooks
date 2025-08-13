<?php
/**
 * Plugin Name: NextJS GraphQL Hooks
 * Plugin URI: https://github.com/SilverAssist/nextjs-graphql-hooks
 * Description: Creates default GraphQL queries for NextJS sites with extensible type registration through filters.
 * Version: 1.0.3
 * Author: Silver Assist
 * Author URI: http://silverassist.com/
 * Text Domain: nextjs-graphql-hooks
 * Domain Path: /languages
 * Requires PHP: 8.0
 * Requires at least: 6.5
 * Tested up to: 6.4
 * Network: false
 * Requires Plugins: wp-graphql, elementor
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * NextJS GraphQL Hooks is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * NextJS GraphQL Hooks is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NextJS GraphQL Hooks. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 *
 * @package NextJSGraphQLHooks
 * @since 1.0.0
 * @version 1.0.3
 * @author Silver Assist
 */

namespace NextJSGraphQLHooks;

// Prevent direct access
defined("ABSPATH") or exit;

// Load Composer autoloader if available
$autoload_file = __DIR__ . "/vendor/autoload.php";
if (file_exists($autoload_file)) {
  require_once $autoload_file;
}

// Define plugin constants
define("NEXTJS_GRAPHQL_HOOKS_VERSION", "1.0.3");
define("NEXTJS_GRAPHQL_HOOKS_PLUGIN_DIR", plugin_dir_path(__FILE__));
define("NEXTJS_GRAPHQL_HOOKS_PLUGIN_URL", plugin_dir_url(__FILE__));
define("NEXTJS_GRAPHQL_HOOKS_PLUGIN_FILE", __FILE__);

/**
 * Main plugin class using Singleton pattern
 */
class NextJS_GraphQL_Hooks
{
	private static ?NextJS_GraphQL_Hooks $instance = null;

	private string $plugin_path;
	private string $plugin_url;

	/**
	 * Get singleton instance
	 *
	 * @return NextJS_GraphQL_Hooks
	 */
	public static function get_instance(): NextJS_GraphQL_Hooks
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct()
	{
		$this->plugin_path = NEXTJS_GRAPHQL_HOOKS_PLUGIN_DIR;
		$this->plugin_url = NEXTJS_GRAPHQL_HOOKS_PLUGIN_URL;

		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks
	 *
	 * @return void
	 */
	private function init_hooks(): void
	{
		\add_action("plugins_loaded", [$this, "load_dependencies"]);
		\add_action("plugins_loaded", [$this, "load_textdomain"]);
		\add_action("init", [$this, "init"]);

		// Initialize GraphQL hooks when WPGraphQL is available
		\add_action("init", [$this, "init_graphql_hooks"], 15);
	}

	/**
	 * Load plugin dependencies
	 *
	 * @return void
	 */
	public function load_dependencies(): void
	{
		// Include the GraphQL hooks class
		require_once NEXTJS_GRAPHQL_HOOKS_PLUGIN_DIR . "includes/GraphQL_Hooks.php";

		// Include the updater class
		require_once NEXTJS_GRAPHQL_HOOKS_PLUGIN_DIR . "includes/Updater.php";
	}

	/**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	public function init(): void
	{
		// Check if WPGraphQL is active
		if (!class_exists("WPGraphQL")) {
			\add_action("admin_notices", [$this, "wpgraphql_missing_notice"]);
			return;
		}

		// Initialize auto-updater
		new Updater(__FILE__, "SilverAssist/nextjs-graphql-hooks");

		// Plugin is ready
		\do_action("nextjs_graphql_hooks_loaded");
	}

	/**
	 * Initialize GraphQL hooks
	 *
	 * @return void
	 */
	public function init_graphql_hooks(): void
	{
		if (class_exists("WPGraphQL")) {
			GraphQL_Hooks::get_instance();
		}
	}

	/**
	 * Load plugin textdomain for translations
	 *
	 * @return void
	 */
	public function load_textdomain(): void
	{
		\load_plugin_textdomain(
			"nextjs-graphql-hooks",
			false,
			dirname(plugin_basename(__FILE__)) . "/languages"
		);
	}

	/**
	 * Display admin notice when WPGraphQL is missing
	 *
	 * @return void
	 */
	public function wpgraphql_missing_notice(): void
	{
		$message = \sprintf(
			\esc_html__("NextJS GraphQL Hooks requires %s to be installed and activated.", "nextjs-graphql-hooks"),
			'<a href="https://wordpress.org/plugins/wp-graphql/" target="_blank">WPGraphQL</a>'
		);

		echo "<div class=\"notice notice-error\"><p>{$message}</p></div>";
	}

	/**
	 * Get plugin path
	 *
	 * @return string
	 */
	public function get_plugin_path(): string
	{
		return $this->plugin_path;
	}

	/**
	 * Get plugin URL
	 *
	 * @return string
	 */
	public function get_plugin_url(): string
	{
		return $this->plugin_url;
	}
}

// Initialize the plugin
NextJS_GraphQL_Hooks::get_instance();
