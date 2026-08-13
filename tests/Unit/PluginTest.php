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
     * Asserts the actual guard flag AbstractPlugin::init() checks, rather
     * than just calling init() twice and asserting true — a passing test
     * here means the guard genuinely prevented a second run, not merely
     * that no exception was thrown.
     *
     * @return void
     */
    public function test_init_is_idempotent(): void
    {
        $plugin = Plugin::instance();
        $plugin->init();

        $initialized_property = new \ReflectionProperty(\SilverAssist\PluginKernel\AbstractPlugin::class, 'initialized');
        $initialized_property->setAccessible(true);

        $this->assertTrue(
            $initialized_property->getValue($plugin),
            'First init() call should set the AbstractPlugin guard flag'
        );

        // A second call must be a no-op: the guard flag must not toggle
        // or otherwise change as a side effect of re-entering init().
        $plugin->init();

        $this->assertTrue(
            $initialized_property->getValue($plugin),
            'Second init() call should leave the guard flag unchanged'
        );
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

    /**
     * Test that init_updater() constructs the Updater and dispatches
     * nextjs_graphql_hooks_loaded when WPGraphQL is active.
     *
     * Every other test in this suite runs against the bare WordPress test
     * environment, where WPGraphQL genuinely isn't installed — so the
     * "WPGraphQL active" success path (updater construction, action
     * dispatch) was previously untested. Stubbing a global WPGraphQL
     * class via eval() would normally leak into every later-run test in
     * the same PHPUnit process (see feedback_wp-tests-extend-wp-unittestcase
     * in project memory), so this test runs in an isolated process instead
     * — the stub only exists for this one test, not the rest of the suite.
     * init_updater() itself is invoked directly via Reflection (it's
     * private) rather than through the full init() flow, since WordPress's
     * own automatic "init" firing during bootstrap happens before this
     * method body runs and would already have gated on WPGraphQL being
     * absent at that point.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @return void
     */
    public function test_init_updater_constructs_updater_when_wpgraphql_active(): void
    {
        if (!class_exists('WPGraphQL')) {
            eval('class WPGraphQL {}');
        }

        $plugin = Plugin::instance();

        $loaded_fired = false;
        \add_action(
            'nextjs_graphql_hooks_loaded',
            function () use (&$loaded_fired) {
                $loaded_fired = true;
            }
        );

        $method = new \ReflectionMethod(Plugin::class, 'init_updater');
        $method->setAccessible(true);
        $method->invoke($plugin);

        $this->assertInstanceOf(\NextJSGraphQLHooks\Updater::class, $plugin->get_updater());
        $this->assertTrue($loaded_fired, 'nextjs_graphql_hooks_loaded should dispatch when WPGraphQL is active');
    }
}
