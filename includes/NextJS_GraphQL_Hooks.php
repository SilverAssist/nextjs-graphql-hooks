<?php
/**
 * Deprecated Compatibility Facade
 *
 * Pre-1.3.0 releases exposed the plugin's root singleton as
 * NextJSGraphQLHooks\NextJS_GraphQL_Hooks, with public accessors for the
 * plugin path/URL, updater, and lifecycle hooks. 1.3.0 replaced that
 * monolithic class with Plugin (extends AbstractPlugin), which self-
 * bootstraps on the "init" action rather than being driven externally.
 *
 * This facade keeps the old class name and public method signatures
 * resolvable for any external code (theme snippets, other plugins) still
 * referencing them directly, forwarding to the new architecture where a
 * real equivalent exists. Lifecycle methods that used to be called
 * externally (init(), load_dependencies(), etc.) are now no-ops, since
 * bootstrap already runs automatically before user code has any chance to
 * call them.
 *
 * @package NextJSGraphQLHooks
 * @since 1.3.0
 * @deprecated 1.3.0 Use Plugin::instance() instead.
 */

namespace NextJSGraphQLHooks;

// Prevent direct access
defined("ABSPATH") or exit;

/**
 * Class NextJS_GraphQL_Hooks
 *
 * @deprecated 1.3.0 Use Plugin::instance() instead.
 */
class NextJS_GraphQL_Hooks
{
    /**
     * Single instance of this facade
     *
     * @var NextJS_GraphQL_Hooks|null
     */
    private static ?NextJS_GraphQL_Hooks $instance = null;

    /**
     * Get the single instance of this facade
     *
     * @deprecated 1.3.0 Use Plugin::instance() instead.
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
    }

    /**
     * No-op: bootstrap now runs automatically on the "init" action.
     *
     * @deprecated 1.3.0 No longer needed — Plugin::instance()->init() is called automatically.
     * @return void
     */
    public function load_dependencies(): void
    {
    }

    /**
     * No-op: bootstrap now runs automatically on the "init" action.
     *
     * @deprecated 1.3.0 No longer needed — Plugin::instance()->init() is called automatically.
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * No-op: GraphQL_Hooks now loads itself via the LoadableInterface component loader.
     *
     * @deprecated 1.3.0 No longer needed — use GraphQL_Hooks::instance() directly if needed.
     * @return void
     */
    public function init_graphql_hooks(): void
    {
    }

    /**
     * No-op: AdminPanel now loads itself via the LoadableInterface component loader.
     *
     * @deprecated 1.3.0 No longer needed — use AdminPanel::instance() directly if needed.
     * @return void
     */
    public function init_admin_panel(): void
    {
    }

    /**
     * No-op: textdomain loading now runs automatically as part of Plugin::init_hooks().
     *
     * @deprecated 1.3.0 No longer needed.
     * @return void
     */
    public function load_textdomain(): void
    {
    }

    /**
     * Display admin notice when WPGraphQL is missing
     *
     * @deprecated 1.3.0 Use Plugin::instance()->wpgraphql_missing_notice() instead.
     * @return void
     */
    public function wpgraphql_missing_notice(): void
    {
        Plugin::instance()->wpgraphql_missing_notice();
    }

    /**
     * Get plugin path
     *
     * @deprecated 1.3.0 Use the NEXTJS_GRAPHQL_HOOKS_PLUGIN_DIR constant instead.
     * @return string
     */
    public function get_plugin_path(): string
    {
        return NEXTJS_GRAPHQL_HOOKS_PLUGIN_DIR;
    }

    /**
     * Get plugin URL
     *
     * @deprecated 1.3.0 Use the NEXTJS_GRAPHQL_HOOKS_PLUGIN_URL constant instead.
     * @return string
     */
    public function get_plugin_url(): string
    {
        return NEXTJS_GRAPHQL_HOOKS_PLUGIN_URL;
    }

    /**
     * Get updater instance
     *
     * @deprecated 1.3.0 Use Plugin::instance()->get_updater() instead.
     * @return Updater|null
     */
    public function get_updater(): ?Updater
    {
        return Plugin::instance()->get_updater();
    }
}
