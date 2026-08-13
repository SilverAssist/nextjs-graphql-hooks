<?php
/**
 * Tests for the deprecated NextJS_GraphQL_Hooks compatibility facade.
 *
 * @package NextJSGraphQLHooks
 * @since 1.3.0
 */

namespace NextJSGraphQLHooks\Tests\Unit;

use NextJSGraphQLHooks\NextJS_GraphQL_Hooks;
use NextJSGraphQLHooks\Plugin;
use WP_UnitTestCase;

/**
 * Test case for the pre-1.3.0 compatibility facade, using the real WordPress test environment.
 *
 * @since 1.3.0
 */
class NextJSGraphQLHooksFacadeTest extends WP_UnitTestCase
{
    /**
     * Test singleton instance creation.
     *
     * @return void
     */
    public function test_get_instance_returns_singleton(): void
    {
        $instance1 = NextJS_GraphQL_Hooks::get_instance();
        $instance2 = NextJS_GraphQL_Hooks::get_instance();

        $this->assertInstanceOf(NextJS_GraphQL_Hooks::class, $instance1);
        $this->assertSame($instance1, $instance2);
    }

    /**
     * Test get_plugin_path returns the plugin directory constant.
     *
     * @return void
     */
    public function test_get_plugin_path_returns_plugin_dir_constant(): void
    {
        $this->assertSame(NEXTJS_GRAPHQL_HOOKS_PLUGIN_DIR, NextJS_GraphQL_Hooks::get_instance()->get_plugin_path());
    }

    /**
     * Test get_plugin_url returns the plugin URL constant.
     *
     * @return void
     */
    public function test_get_plugin_url_returns_plugin_url_constant(): void
    {
        $this->assertSame(NEXTJS_GRAPHQL_HOOKS_PLUGIN_URL, NextJS_GraphQL_Hooks::get_instance()->get_plugin_url());
    }

    /**
     * Test get_updater forwards to Plugin::instance()->get_updater().
     *
     * @return void
     */
    public function test_get_updater_forwards_to_plugin(): void
    {
        $this->assertSame(Plugin::instance()->get_updater(), NextJS_GraphQL_Hooks::get_instance()->get_updater());
    }

    /**
     * Test the lifecycle no-op methods don't throw.
     *
     * @return void
     */
    public function test_lifecycle_methods_are_safe_no_ops(): void
    {
        $facade = NextJS_GraphQL_Hooks::get_instance();

        $facade->load_dependencies();
        $facade->init();
        $facade->init_graphql_hooks();
        $facade->init_admin_panel();
        $facade->load_textdomain();

        $this->assertTrue(true);
    }

    /**
     * Test wpgraphql_missing_notice forwards to Plugin and outputs the same message.
     *
     * @return void
     */
    public function test_wpgraphql_missing_notice_forwards_to_plugin(): void
    {
        ob_start();
        NextJS_GraphQL_Hooks::get_instance()->wpgraphql_missing_notice();
        $output = ob_get_clean();

        $this->assertStringContainsString('NextJS GraphQL Hooks requires', $output);
    }
}
