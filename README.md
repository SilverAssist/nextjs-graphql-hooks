# NextJS GraphQL Hooks

A WordPress plugin that creates default GraphQL queries for NextJS sites with extensible type registration through filters.

## Description

NextJS GraphQL Hooks provides essential GraphQL queries and types that are commonly needed when building NextJS sites with WordPress as a headless CMS. The plugin follows modern PHP 8.0+ standards and uses the singleton pattern for efficient resource management.

## Features

- **Default Page Fields**: Automatically adds `elementorContent` and `elementorCSSFile` fields to Page queries
- **Elementor Integration**: Provides `elementorLibraryKit` query for global Elementor styles
- **Extensible Architecture**: Use filters to register custom GraphQL types and fields
- **Modern PHP 8.0+**: Built with modern PHP features including typed properties, match expressions, and namespaces
- **Singleton Pattern**: Efficient resource management with singleton instances
- **Error Handling**: Comprehensive error logging and graceful fallbacks

## Requirements

- WordPress 5.0+
- PHP 8.0+
- WPGraphQL plugin
- Elementor (optional, for Elementor-related features)

## Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/nextjs-graphql-hooks/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Ensure WPGraphQL is installed and activated

## Default GraphQL Queries

### Page Fields

```graphql
query GetPage($id: ID!) {
  page(id: $id) {
    id
    title
    content
    elementorContent(css: false)
    elementorCSSFile
  }
}
```

### Elementor Library Kit

```graphql
query GetElementorKit {
  elementorLibraryKit {
    kit_id
    css_file
  }
}
```

## Extending the Plugin

The plugin provides a filter system that allows you to register custom GraphQL types and fields. Use the `nextjs_graphql_hooks_register_types` action hook to extend functionality.

### Example: Adding Custom Types

```php
add_action('nextjs_graphql_hooks_register_types', function ($type_registry, $hooks_instance) {
    // Register a custom object type
    $hooks_instance->register_custom_object_type('CustomType', [
        'description' => __('A custom GraphQL type', 'your-textdomain'),
        'fields' => [
            'custom_field' => [
                'type' => 'String',
                'description' => __('A custom field', 'your-textdomain'),
            ],
        ],
    ]);

    // Register a root query field
    $hooks_instance->register_root_query_field($type_registry, 'customQuery', [
        'type' => 'CustomType',
        'description' => __('A custom query', 'your-textdomain'),
        'resolve' => function () {
            return ['custom_field' => 'Custom value'];
        }
    ]);
}, 10, 2);
```

### Available Helper Methods

#### `register_custom_object_type($type_name, $config)`
Register a custom GraphQL object type.

#### `register_custom_fields($type_registry, $type_name, $fields)`
Add custom fields to an existing GraphQL type.

#### `register_root_query_field($type_registry, $field_name, $config)`
Register a field in the root query.

## File Structure

```
nextjs-graphql-hooks/
├── nextjs-graphql-hooks.php          # Main plugin file
├── includes/
│   └── graphql-hooks.php             # GraphQL hooks class
├── languages/                        # Translation files
└── README.md                         # This file
```

## Hooks and Filters

### Actions

- `nextjs_graphql_hooks_loaded` - Fired when the plugin is fully loaded
- `nextjs_graphql_hooks_register_types` - Use this to register custom GraphQL types

### Example Usage in functions.php

```php
// Add this to your theme's functions.php file
add_action('nextjs_graphql_hooks_register_types', function ($type_registry, $hooks_instance) {
    // Your custom type registration here
    // See examples/custom-types-example.php for detailed examples
}, 10, 2);
```

## Error Handling

The plugin includes comprehensive error handling:

- Graceful fallbacks when Elementor is not available
- Error logging for debugging purposes
- Empty string returns instead of exceptions for missing content

## Development

### PHP Coding Standards

- **Double quotes** for all strings: `"string"` not `'string'`
- **Short array syntax**: `[]` not `array()`
- **Namespaces**: `NextJSGraphQLHooks`
- **Singleton pattern**: `Class_Name::get_instance()`
- **WordPress hooks**: `\add_action("init", [$this, "method"])`
- **PHP 8+ Features**: Match expressions, array spread, typed properties
- **Global function calls**: Use `\` prefix for WordPress functions in namespaced context

### Modern PHP 8.0+ Features

- **Namespace Organization**: Clean namespace structure
- **Match Expression**: For conditional logic
- **Array Spread**: Efficient array building
- **Typed Properties**: Strong typing with nullable types
- **Return Type Declarations**: Explicit return types
- **Null Coalescing Operator**: Safe null handling

## Contributing

1. Follow the established coding standards
2. Add proper PHPDoc comments
3. Include error handling
4. Test with both Elementor enabled and disabled
5. Ensure compatibility with WPGraphQL latest version

## License

GPL v2 or later

## Author

Silver Assist  
Website: http://silverassist.com/

## Support

For support and feature requests, please contact Silver Assist or open an issue in the project repository.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed version history and changes.
