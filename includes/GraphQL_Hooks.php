<?php
/**
 * GraphQL Hooks class for NextJS integration
 *
 * @package NextJSGraphQLHooks
 * @since 1.0.0
 * @author Silver Assist
 * @version 1.0.0
 * @license GPL v2 or later
 */

namespace NextJSGraphQLHooks;

use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Plugin as Elementor;
use WP_Error;
use WPGraphQL\Model\Post;
use WPGraphQL\Registry\TypeRegistry;

// Prevent direct access
defined("ABSPATH") or exit;

/**
 * Class GraphQL_Hooks
 *
 * Handles the registration and callbacks for GraphQL types and fields.
 */
class GraphQL_Hooks
{
    private static ?GraphQL_Hooks $instance = null;

    /**
     * Get the singleton instance
     *
     * @return GraphQL_Hooks
     */
    public static function get_instance(): GraphQL_Hooks
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * GraphQL_Hooks constructor.
     *
     * Register the GraphQL types and fields.
     */
    private function __construct()
    {
        // Register the GraphQL types
        \add_action("graphql_register_types", [$this, "register_graphql_types"]);
    }

    /**
     * Register default GraphQL types and fields.
     *
     * @param TypeRegistry $type_registry The WPGraphQL type registry instance.
     * @return void
     */
    public function register_graphql_types(TypeRegistry $type_registry): void
    {
        // Register default Page fields
        $this->register_page_fields($type_registry);

        // Register Elementor Library Kit field
        $this->register_elementor_library_kit_field($type_registry);

        // Allow other plugins/themes to register additional types
        \do_action("nextjs_graphql_hooks_register_types", $type_registry, $this);
    }

    /**
     * Register Page fields for Elementor content
     *
     * @param TypeRegistry $type_registry The WPGraphQL type registry instance.
     * @return void
     */
    private function register_page_fields(TypeRegistry $type_registry): void
    {
        $type_registry->register_fields(
            type_name: "Page",
            fields: [
                "elementorContent" => [
                    "type" => "String",
                    "description" => \__("Elementor content for the page", "nextjs-graphql-hooks"),
                    "args" => [
                        "css" => [
                            "type" => "Boolean",
                            "description" => \__("Whether to include the CSS inline", "nextjs-graphql-hooks"),
                            "default" => false,
                        ],
                    ],
                    "resolve" => function ($post, $args): string {
                        return $this->get_elementor_content($post, $args);
                    }
                ],
                "elementorCSSFile" => [
                    "type" => "String",
                    "description" => \__("Elementor CSS file URL", "nextjs-graphql-hooks"),
                    "resolve" => function ($post): string {
                        return $this->get_elementor_css_file($post);
                    }
                ]
            ]
        );
    }

    /**
     * Register Elementor Library Kit field
     *
     * @param TypeRegistry $type_registry The WPGraphQL type registry instance.
     * @return void
     */
    private function register_elementor_library_kit_field(TypeRegistry $type_registry): void
    {
        // Register ElementorLibraryKit object type
        \register_graphql_object_type("ElementorLibraryKit", [
            "description" => \__("Elementor Library Kit Type", "nextjs-graphql-hooks"),
            "fields" => [
                "kit_id" => [
                    "type" => "String",
                    "description" => \__("The ID of the Elementor Library Kit", "nextjs-graphql-hooks"),
                ],
                "css_file" => [
                    "type" => "String",
                    "description" => \__("The CSS file URL of the Elementor Library Kit", "nextjs-graphql-hooks"),
                ],
            ],
        ]);

        // Register the field in RootQuery
        $type_registry->register_field("RootQuery", "elementorLibraryKit", [
            "type" => "ElementorLibraryKit",
            "description" => \__("Elementor library kit information", "nextjs-graphql-hooks"),
            "resolve" => function (): array {
                return $this->get_elementor_library_kit();
            }
        ]);
    }

    /**
     * Get the Elementor content for the page.
     *
     * @param Post $post The post object.
     * @param array $args The arguments.
     * @return string The Elementor content.
     */
    private function get_elementor_content(Post $post, array $args): string
    {
        if (!\class_exists("\Elementor\Plugin")) {
            return "";
        }

        try {
            $elementor = Elementor::instance();
            $document = $elementor->documents->get($post->ID);

            if (!$document) {
                return "";
            }

            $content = $document->get_content($args["css"] ?? false);
            return $content ?: "";
        } catch (\Exception $e) {
            \error_log("NextJS GraphQL Hooks - Elementor content error: " . $e->getMessage());
            return "";
        }
    }

    /**
     * Get the Elementor CSS file for the page.
     *
     * @param Post $post The post object.
     * @return string The Elementor CSS file URL.
     */
    private function get_elementor_css_file(Post $post): string
    {
        if (!\class_exists("\Elementor\Core\Files\CSS\Post")) {
            return "";
        }

        try {
            $css_file = Post_CSS::create($post->ID);
            $css_file_url = $css_file->get_url();
            return $css_file_url ?: "";
        } catch (\Exception $e) {
            \error_log("NextJS GraphQL Hooks - Elementor CSS file error: " . $e->getMessage());
            return "";
        }
    }

    /**
     * Get the Elementor library kit information.
     *
     * @return array The kit information array.
     */
    private function get_elementor_library_kit(): array
    {
        if (!\class_exists("\Elementor\Plugin")) {
            return [
                "kit_id" => "",
                "css_file" => ""
            ];
        }

        try {
            $elementor = Elementor::instance();
            $kit_id = $elementor->kits_manager->get_active_id();

            if (!$kit_id) {
                return [
                    "kit_id" => "",
                    "css_file" => ""
                ];
            }

            $css_file = Post_CSS::create($kit_id);
            $css_file_url = $css_file->get_url();

            return [
                "kit_id" => (string) $kit_id,
                "css_file" => $css_file_url ?: ""
            ];
        } catch (\Exception $e) {
            \error_log("NextJS GraphQL Hooks - Elementor library kit error: " . $e->getMessage());
            return [
                "kit_id" => "",
                "css_file" => ""
            ];
        }
    }

    /**
     * Helper method to register custom GraphQL object types
     * This can be used by other plugins/themes through the filter
     *
     * @param string $type_name The name of the GraphQL type.
     * @param array $config The type configuration.
     * @return void
     */
    public function register_custom_object_type(string $type_name, array $config): void
    {
        \register_graphql_object_type($type_name, $config);
    }

    /**
     * Helper method to register custom GraphQL fields
     * This can be used by other plugins/themes through the filter
     *
     * @param TypeRegistry $type_registry The WPGraphQL type registry instance.
     * @param string $type_name The type to add fields to.
     * @param array $fields The fields configuration.
     * @return void
     */
    public function register_custom_fields(TypeRegistry $type_registry, string $type_name, array $fields): void
    {
        $type_registry->register_fields($type_name, $fields);
    }

    /**
     * Helper method to register root query fields
     * This can be used by other plugins/themes through the filter
     *
     * @param TypeRegistry $type_registry The WPGraphQL type registry instance.
     * @param string $field_name The field name.
     * @param array $config The field configuration.
     * @return void
     */
    public function register_root_query_field(TypeRegistry $type_registry, string $field_name, array $config): void
    {
        $type_registry->register_field("RootQuery", $field_name, $config);
    }
}
