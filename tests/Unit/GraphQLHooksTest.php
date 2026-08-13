<?php
/**
 * Tests for the GraphQL_Hooks component.
 *
 * @package NextJSGraphQLHooks
 * @since 1.3.0
 */

namespace NextJSGraphQLHooks\Tests\Unit;

use NextJSGraphQLHooks\GraphQL_Hooks;
use WP_UnitTestCase;

/**
 * Test case for GraphQL_Hooks, using the real WordPress test environment.
 *
 * @since 1.3.0
 */
class GraphQLHooksTest extends WP_UnitTestCase
{
    /**
     * Test singleton instance creation.
     *
     * @return void
     */
    public function test_instance_returns_singleton(): void
    {
        $instance1 = GraphQL_Hooks::instance();
        $instance2 = GraphQL_Hooks::instance();

        $this->assertInstanceOf(GraphQL_Hooks::class, $instance1);
        $this->assertSame($instance1, $instance2);
    }

    /**
     * Test the deprecated get_instance() alias forwards to instance().
     *
     * @return void
     */
    public function test_deprecated_get_instance_forwards_to_instance(): void
    {
        $this->assertSame(GraphQL_Hooks::instance(), GraphQL_Hooks::get_instance());
    }

    /**
     * Test that GraphQL_Hooks implements the shared LoadableInterface.
     *
     * @return void
     */
    public function test_implements_loadable_interface(): void
    {
        $this->assertInstanceOf(
            \SilverAssist\PluginKernel\Interfaces\LoadableInterface::class,
            GraphQL_Hooks::instance()
        );
    }

    /**
     * Test get_priority returns the Services-tier value.
     *
     * @return void
     */
    public function test_get_priority_returns_expected_value(): void
    {
        $this->assertSame(20, GraphQL_Hooks::instance()->get_priority());
    }

    /**
     * Test should_load reflects whether WPGraphQL is active.
     *
     * The bare WordPress test environment doesn't have WPGraphQL
     * installed, so this exercises the real "not available" branch.
     *
     * @return void
     */
    public function test_should_load_reflects_wpgraphql_availability(): void
    {
        $this->assertSame(class_exists('WPGraphQL'), GraphQL_Hooks::instance()->should_load());
    }

    /**
     * Test init() registers the graphql_register_types hook.
     *
     * @return void
     */
    public function test_init_registers_graphql_register_types_hook(): void
    {
        $instance = GraphQL_Hooks::instance();
        $instance->init();

        $this->assertGreaterThan(
            0,
            has_action('graphql_register_types', [$instance, 'register_graphql_types'])
        );
    }
}
