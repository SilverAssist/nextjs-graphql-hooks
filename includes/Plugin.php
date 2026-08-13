<?php
/**
 * Main Plugin Class
 *
 * Handles plugin initialization and coordinates between components.
 *
 * @package NextJSGraphQLHooks
 * @since 1.3.0
 * @version 1.3.0
 * @author Silver Assist
 */

namespace NextJSGraphQLHooks;

use SilverAssist\PluginKernel\AbstractPlugin;

// Prevent direct access
defined("ABSPATH") or exit;

/**
 * Class Plugin
 *
 * Singleton access (instance()) and the priority-ordered component loading
 * loop are inherited from AbstractPlugin (silverassist/wp-plugin-kernel) —
 * this class only declares which components to load (get_components()) and
 * the plugin-specific setup that runs alongside them (init_hooks()).
 *
 * Extracted from the plugin's former monolithic NextJS_GraphQL_Hooks class,
 * which lived directly in the main plugin file.
 *
 * @since 1.3.0
 */
class Plugin extends AbstractPlugin
{
    /**
     * Updater instance
     *
     * @var Updater|null
     */
    private ?Updater $updater = null;

    /**
     * List the component classes this plugin loads
     *
     * Loading order is determined by each component's get_priority(), not
     * by the order they're listed here.
     *
     * @since 1.3.0
     * @return array<class-string>
     */
    protected function get_components(): array
    {
        return [
            GraphQL_Hooks::class,
            AdminPanel::class,
        ];
    }

    /**
     * Plugin-level setup that isn't itself a LoadableInterface component
     *
     * Runs after all components have loaded.
     *
     * @since 1.3.0
     * @return void
     */
    protected function init_hooks(): void
    {
        $this->load_textdomain();
        $this->init_updater();
    }

    /**
     * Load plugin textdomain for translations
     *
     * @since 1.3.0
     * @return void
     */
    private function load_textdomain(): void
    {
        \load_plugin_textdomain(
            "nextjs-graphql-hooks",
            false,
            \dirname(\plugin_basename(NEXTJS_GRAPHQL_HOOKS_PLUGIN_FILE)) . "/languages"
        );
    }

    /**
     * Initialize auto-updater
     *
     * WPGraphQL isn't required for the plugin to load — GraphQL_Hooks
     * itself already gates on class_exists("WPGraphQL") via should_load()
     * — but the original behavior only ran the updater (and dispatched
     * nextjs_graphql_hooks_loaded) when WPGraphQL was active, showing an
     * admin notice instead when it was missing, so that distinction is
     * preserved here.
     *
     * @since 1.3.0
     * @return void
     */
    private function init_updater(): void
    {
        if (!\class_exists("WPGraphQL")) {
            \add_action("admin_notices", [$this, "wpgraphql_missing_notice"]);
            return;
        }

        $this->updater = new Updater(NEXTJS_GRAPHQL_HOOKS_PLUGIN_FILE, "SilverAssist/nextjs-graphql-hooks");

        \do_action("nextjs_graphql_hooks_loaded");
    }

    /**
     * Display admin notice when WPGraphQL is missing
     *
     * @since 1.3.0
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
     * Get Updater instance
     *
     * @since 1.3.0
     * @return Updater|null
     */
    public function get_updater(): ?Updater
    {
        return $this->updater;
    }
}
