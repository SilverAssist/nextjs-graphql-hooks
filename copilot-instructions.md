# Copilot Instructions for NextJS GraphQL Hooks Plugin

## Plugin Overview

NextJS GraphQL Hooks is a WordPress plugin that provides essential GraphQL queries for NextJS sites using WordPress as a headless CMS. The plugin is built with modern PHP 8.0+ features and follows WordPress coding standards.

## Core Architecture

### Main Components

1. **Main Plugin Class**: `NextJSGraphQLHooks\NextJS_GraphQL_Hooks`
   - Singleton pattern implementation
   - Handles plugin initialization and dependency checks
   - Located in: `nextjs-graphql-hooks.php`

2. **GraphQL Hooks Class**: `NextJSGraphQLHooks\GraphQL_Hooks`
   - Manages GraphQL type and field registration
   - Provides helper methods for extensibility
   - Located in: `includes/class-graphql-hooks.php`

### Key Features

- **Default Fields**: Automatically adds Elementor-related fields to Page queries
- **Extensible**: Filter system allows custom type registration
- **Error Handling**: Comprehensive error logging and graceful fallbacks
- **Modern PHP**: Uses PHP 8.0+ features including typed properties and namespaces

## Coding Standards

### PHP Coding Guidelines

```php
// ✅ Correct - Use double quotes
$message = "Hello World";

// ❌ Incorrect - Single quotes
$message = 'Hello World';

// ✅ Correct - Short array syntax
$array = ["item1", "item2"];

// ❌ Incorrect - Old array syntax
$array = array("item1", "item2");

// ✅ Correct - Namespace usage
namespace NextJSGraphQLHooks;

// ✅ Correct - Global function calls in namespace
\add_action("init", [$this, "method"]);

// ✅ Correct - Typed properties
private static ?NextJS_GraphQL_Hooks $instance = null;

// ✅ Correct - Return type declarations
public function get_instance(): NextJS_GraphQL_Hooks
```

### WordPress Integration

```php
// ✅ Correct - WordPress hooks in namespaced context
\add_action("graphql_register_types", [$this, "register_graphql_types"]);

// ✅ Correct - Internationalization
\__("Text to translate", "nextjs-graphql-hooks");

// ✅ Correct - WordPress functions with namespace prefix
\register_graphql_object_type($type_name, $config);
```

## File Structure

```
nextjs-graphql-hooks/
├── nextjs-graphql-hooks.php          # Main plugin file
├── includes/
│   └── class-graphql-hooks.php       # Core GraphQL functionality
├── examples/
│   └── custom-types-example.php      # Usage examples
├── languages/                        # Translation files (future)
├── README.md                         # Documentation
└── copilot-instructions.md           # This file
```

## Development Guidelines

### Adding New Features

1. **Follow Singleton Pattern**: Use `get_instance()` for class instantiation
2. **Use Filters**: Implement `nextjs_graphql_hooks_register_types` action for extensibility
3. **Error Handling**: Always include try-catch blocks for external API calls
4. **Type Safety**: Use PHP 8+ typed properties and return types

### Example: Adding a New GraphQL Type

```php
// In the register_graphql_types method
public function register_graphql_types(TypeRegistry $type_registry): void
{
    // Register core types first
    $this->register_page_fields($type_registry);
    $this->register_elementor_library_kit_field($type_registry);
    
    // Allow extensions via filter
    \do_action("nextjs_graphql_hooks_register_types", $type_registry, $this);
}
```

### Example: Using the Filter System

```php
// In functions.php or another plugin
\add_action("nextjs_graphql_hooks_register_types", function ($type_registry, $hooks_instance): void {
    $hooks_instance->register_custom_object_type("CustomType", [
        "description" => \__("Custom type description", "nextjs-graphql-hooks"),
        "fields" => [
            "custom_field" => [
                "type" => "String",
                "description" => \__("Custom field description", "nextjs-graphql-hooks"),
            ],
        ],
    ]);
}, 10, 2);
```

## Helper Methods

### Available Helper Methods in GraphQL_Hooks

1. **`register_custom_object_type($type_name, $config)`**
   - Registers a new GraphQL object type
   - Use for creating custom data structures

2. **`register_custom_fields($type_registry, $type_name, $fields)`**
   - Adds fields to existing GraphQL types
   - Use for extending Post, Page, or custom post types

3. **`register_root_query_field($type_registry, $field_name, $config)`**
   - Adds fields to the root query
   - Use for top-level queries like `advisor` or `advisors`

## Error Handling Patterns

### Elementor Integration

```php
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
```

## Dependencies

### Required

- **WordPress**: 5.0+
- **PHP**: 8.0+
- **WPGraphQL**: Latest version

### Optional

- **Elementor**: For Elementor-related functionality

## Testing Considerations

### Manual Testing

1. **With Elementor Active**:
   - Test `elementorContent` field returns content
   - Test `elementorCSSFile` field returns valid URL
   - Test `elementorLibraryKit` returns kit information

2. **Without Elementor**:
   - Test fields return empty strings instead of errors
   - Test no PHP errors in logs

3. **Custom Extensions**:
   - Test filter system works correctly
   - Test custom types appear in GraphQL schema

### GraphQL Query Examples

```graphql
# Test default Page fields
query GetPage($id: ID!) {
  page(id: $id) {
    elementorContent
    elementorCSSFile
  }
}

# Test Elementor Library Kit
query GetKit {
  elementorLibraryKit {
    kit_id
    css_file
  }
}
```

## Common Patterns

### Adding Post Meta Fields

```php
$hooks_instance->register_custom_fields($type_registry, "Post", [
    "customMeta" => [
        "type" => "String",
        "description" => \__("Custom meta field", "nextjs-graphql-hooks"),
        "resolve" => function ($post) {
            $meta = \get_post_meta($post->databaseId, "custom_meta_key", true);
            return !empty($meta) ? $meta : null;
        }
    ]
]);
```

### Creating List Types

```php
$hooks_instance->register_custom_object_type("ListItem", [
    "fields" => [
        "title" => ["type" => "String"],
        "value" => ["type" => "String"],
    ],
]);

// Use in another type
"items" => [
    "type" => ["list_of" => "ListItem"],
    "description" => \__("List of items", "nextjs-graphql-hooks"),
],
```

## Security Considerations

1. **Input Sanitization**: Always sanitize GraphQL arguments
2. **Error Messages**: Don't expose sensitive information in error messages
3. **Capability Checks**: Implement proper WordPress capability checks when needed
4. **Data Validation**: Validate data before processing

## Future Enhancements

### Planned Features

1. **Translation Support**: Complete internationalization
2. **Admin Interface**: Settings page for configuration
3. **Query Caching**: Implement caching for expensive queries
4. **Performance Monitoring**: Add query performance tracking

### Extensibility Points

1. **Custom Resolvers**: Allow custom resolver registration
2. **Field Validation**: Add field validation hooks
3. **Query Optimization**: Implement query optimization filters
4. **Schema Customization**: Allow schema modification hooks

## Best Practices

1. **Always use the filter system** for extensions
2. **Include comprehensive error handling**
3. **Follow WordPress coding standards**
4. **Use proper text domains for translations**
5. **Document all public methods and hooks**
6. **Test with both required and optional dependencies**
