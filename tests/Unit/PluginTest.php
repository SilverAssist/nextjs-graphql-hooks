<?php
/**
 * Tests for the Plugin bootstrap class.
 *
 * @package NextJSGraphQLHooks
 * @since 1.3.0
 */

namespace NextJSGraphQLHooks\Tests\Unit;

use NextJSGraphQLHooks\AdminPanel;
use NextJSGraphQLHooks\GraphQL_Hooks;
use NextJSGraphQLHooks\Plugin;
use WP_UnitTestCase;

/**
 * Test case for Plugin, using the real WordPress test environment.
 *
 * @since 1.3.0
 */
class PluginTest extends WP_UnitTestCase
{
    /**
     * Test singleton instance creation.
     *
     * @return void
     */
    public function test_instance_returns_singleton(): void
    {
        $instance1 = Plugin::instance();
        $instance2 = Plugin::instance();

        $this->assertInstanceOf(Plugin::class, $instance1);
        $this->assertSame($instance1, $instance2, 'Plugin::instance() should return the same instance');
    }

    /**
     * Test that Plugin implements the shared LoadableInterface.
     *
     * @return void
     */
    public function test_implements_loadable_interface(): void
    {
        $this->assertInstanceOf(
            \SilverAssist\PluginKernel\Interfaces\LoadableInterface::class,
            Plugin::instance()
        );
    }

    /**
     * Test that Plugin::get_components() lists both LoadableInterface components.
     *
     * get_components() is protected per AbstractPlugin's contract, so it's
     * invoked via Reflection here. Regression coverage for the loader
     * wiring itself, not just each component in isolation.
     *
     * @return void
     */
    public function test_get_components_lists_graphql_hooks_and_admin_panel(): void
    {
        $method = new \ReflectionMethod(Plugin::class, 'get_components');
        $method->setAccessible(true);
        $components = $method->invoke(Plugin::instance());

        $this->assertContains(GraphQL_Hooks::class, $components);
        $this->assertContains(AdminPanel::class, $components);
    }

    /**
     * Test that init() is idempotent (guarded by AbstractPlugin).
     *
     * @return void
     */
    public function test_init_is_idempotent(): void
    {
        $plugin = Plugin::instance();

        // Should not throw or re-register hooks on a second call.
        $plugin->init();
        $plugin->init();

        $this->assertTrue(true);
    }

    /**
     * Test that get_updater() returns null when WPGraphQL isn't active.
     *
     * The bare WordPress test environment doesn't have WPGraphQL
     * installed, so the updater should never have been constructed.
     *
     * @return void
     */
    public function test_get_updater_returns_null_without_wpgraphql(): void
    {
        $this->assertFalse(class_exists('WPGraphQL'), 'Precondition: WPGraphQL is not active in this test environment');

        Plugin::instance()->init();

        $this->assertNull(Plugin::instance()->get_updater());
    }

    /**
     * Test that the WPGraphQL-missing admin notice renders the expected message.
     *
     * @return void
     */
    public function test_wpgraphql_missing_notice_outputs_message(): void
    {
        ob_start();
        Plugin::instance()->wpgraphql_missing_notice();
        $output = ob_get_clean();

        $this->assertStringContainsString('NextJS GraphQL Hooks requires', $output);
        $this->assertStringContainsString('WPGraphQL', $output);
    }
}
