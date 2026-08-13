<?php
/**
 * Plugin Name: NextJS GraphQL Hooks
 * Plugin URI: https://github.com/SilverAssist/nextjs-graphql-hooks
 * Description: Creates default GraphQL queries for NextJS sites with extensible type registration through filters.
 * Version: 1.3.0
 * Author: Silver Assist
 * Author URI: http://silverassist.com/
 * Text Domain: nextjs-graphql-hooks
 * Domain Path: /languages
 * Requires PHP: 8.2
 * Requires at least: 6.5
 * Tested up to: 6.4
 * Network: false
 * Requires Plugins: wp-graphql, elementor
 * License: Polyform Noncommercial License 1.0.0
 * License URI: https://polyformproject.org/licenses/noncommercial/1.0.0/
 *
 * @package NextJSGraphQLHooks
 * @since 1.0.0
 * @version 1.3.0
 * @author Silver Assist
 */

namespace NextJSGraphQLHooks;

// Prevent direct access
defined("ABSPATH") or exit;

// Define plugin constants
define("NEXTJS_GRAPHQL_HOOKS_VERSION", "1.3.0");
define("NEXTJS_GRAPHQL_HOOKS_PLUGIN_DIR", plugin_dir_path(__FILE__));
define("NEXTJS_GRAPHQL_HOOKS_PLUGIN_URL", plugin_dir_url(__FILE__));
define("NEXTJS_GRAPHQL_HOOKS_PLUGIN_FILE", __FILE__);

// Load Composer autoloader for external packages and this plugin's own PSR-4-mapped classes.
if (file_exists(NEXTJS_GRAPHQL_HOOKS_PLUGIN_DIR . "vendor/autoload.php")) {
    require_once NEXTJS_GRAPHQL_HOOKS_PLUGIN_DIR . "vendor/autoload.php";
}

/**
 * Initialize the plugin on the "init" action rather than "plugins_loaded".
 *
 * GraphQL_Hooks::should_load() depends on class_exists("WPGraphQL") having
 * already been declared. WPGraphQL typically finishes setting up its main
 * class from its own plugins_loaded callback, and plugin load order across
 * a site isn't guaranteed — evaluating should_load() during this plugin's
 * own plugins_loaded callback risks running before WPGraphQL has announced
 * itself. By "init", every plugin's plugins_loaded callback has already
 * run, so the check is reliable. None of this plugin's components need
 * plugins_loaded-specific timing.
 */
\add_action(
    "init",
    static function () {
        Plugin::instance()->init();
    }
);
