<?php
/**
 * Tests for the AdminPanel component.
 *
 * @package NextJSGraphQLHooks
 * @since 1.3.0
 */

namespace NextJSGraphQLHooks\Tests\Unit;

use NextJSGraphQLHooks\AdminPanel;
use WP_UnitTestCase;

/**
 * Test case for AdminPanel, using the real WordPress test environment.
 *
 * @since 1.3.0
 */
class AdminPanelTest extends WP_UnitTestCase
{
    /**
     * Test singleton instance creation.
     *
     * @return void
     */
    public function test_instance_returns_singleton(): void
    {
        $instance1 = AdminPanel::instance();
        $instance2 = AdminPanel::instance();

        $this->assertInstanceOf(AdminPanel::class, $instance1);
        $this->assertSame($instance1, $instance2);
    }

    /**
     * Test the deprecated get_instance() alias forwards to instance().
     *
     * @return void
     */
    public function test_deprecated_get_instance_forwards_to_instance(): void
    {
        $this->assertSame(AdminPanel::instance(), AdminPanel::get_instance());
    }

    /**
     * Test that AdminPanel implements the shared LoadableInterface.
     *
     * @return void
     */
    public function test_implements_loadable_interface(): void
    {
        $this->assertInstanceOf(
            \SilverAssist\PluginKernel\Interfaces\LoadableInterface::class,
            AdminPanel::instance()
        );
    }

    /**
     * Test get_priority returns the Admin-tier value.
     *
     * @return void
     */
    public function test_get_priority_returns_expected_value(): void
    {
        $this->assertSame(30, AdminPanel::instance()->get_priority());
    }

    /**
     * Test should_load tracks is_admin(), both outside and inside admin context.
     *
     * @return void
     */
    public function test_should_load_tracks_is_admin(): void
    {
        $this->assertFalse(is_admin(), 'Precondition: this test starts outside admin context');
        $this->assertFalse(AdminPanel::instance()->should_load());

        set_current_screen('dashboard');

        try {
            $this->assertTrue(is_admin(), 'Precondition: set_current_screen() should switch to admin context');
            $this->assertTrue(AdminPanel::instance()->should_load());
        } finally {
            set_current_screen('front');
        }
    }

    /**
     * Test init() registers the admin_menu and admin_enqueue_scripts hooks.
     *
     * @return void
     */
    public function test_init_registers_admin_hooks(): void
    {
        $instance = AdminPanel::instance();
        $instance->init();

        $this->assertSame(4, has_action('admin_menu', [$instance, 'register_with_hub']));
        $this->assertGreaterThan(0, has_action('admin_enqueue_scripts', [$instance, 'enqueue_admin_scripts']));
    }

    /**
     * Test render_admin_page() dies for users without manage_options.
     *
     * @return void
     */
    public function test_render_admin_page_denies_without_capability(): void
    {
        $this->expectException(\WPDieException::class);

        AdminPanel::instance()->render_admin_page();
    }
}
