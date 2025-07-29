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
- **Auto-Update System**: Automatic updates from GitHub releases
- **GitHub Workflows**: Automated quality checks and release management

## Requirements

- WordPress 5.0+
- PHP 8.0+
- WPGraphQL plugin
- Elementor (optional, for Elementor-related features)

## Download

The plugin is available as a ready-to-install ZIP file from GitHub releases:
- **Latest Version**: 1.0.0
- **Package Size**: ~13KB (compressed)
- **Compatibility**: WordPress 5.0+ with PHP 8.0+
- **Auto-Updates**: Included from GitHub releases

📦 **[Download Latest Release](https://github.com/SilverAssist/nextjs-graphql-hooks/releases/latest)**

## Installation

### Method 1: WordPress Admin Dashboard (Recommended)

1. **Download the Plugin**: Download the `nextjs-graphql-hooks-v1.0.0.zip` file from the [releases page](https://github.com/SilverAssist/nextjs-graphql-hooks/releases)
2. **Access WordPress Admin**: Log in to your WordPress admin dashboard
3. **Navigate to Plugins**: Go to `Plugins` → `Add New`
4. **Upload Plugin**: Click the `Upload Plugin` button at the top of the page
5. **Choose File**: Click `Choose File` and select the downloaded ZIP file
6. **Install**: Click `Install Now` and wait for the upload to complete
7. **Activate**: Click `Activate Plugin` to enable the NextJS GraphQL Hooks plugin

### Method 2: Manual Installation via FTP

1. **Extract the ZIP**: Unzip the downloaded file on your computer
2. **Upload via FTP**: Upload the extracted `nextjs-graphql-hooks` folder to the `/wp-content/plugins/` directory on your server
3. **Activate**: Go to the WordPress admin panel and activate the plugin from the `Plugins` page

### Method 3: WP-CLI Installation

If you have WP-CLI installed, you can also install the plugin using the command line:

```bash
# Download and install the plugin
wp plugin install https://github.com/SilverAssist/nextjs-graphql-hooks/releases/latest/download/nextjs-graphql-hooks-v1.0.0.zip --activate

# Or manually activate after uploading
wp plugin activate nextjs-graphql-hooks
```

### Verification

After installation, you should see:
- **GraphQL Fields**: `elementorContent` and `elementorCSSFile` fields available in Page queries
- **Custom Queries**: `elementorLibraryKit` query available in GraphQL
- **Auto-Updates**: Update notifications in WordPress admin when new releases are available
- **Settings Page**: "GraphQL Hooks Updates" under Settings menu for manual update checks

### Auto-Updates
The plugin includes an auto-update system that checks for new releases on GitHub. Updates can be installed directly from the WordPress admin panel under **Dashboard** → **Updates**.

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
│   └── GraphQL_Hooks.php             # GraphQL hooks class
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

## Development & Release Process

### Documentation
- [Release Process Guide](RELEASE-PROCESS.md) - Complete release workflow
- [Update System Guide](UPDATE-SYSTEM.md) - Auto-update system details
- [Quick Release Guide](QUICK-RELEASE.md) - Emergency release procedures

### GitHub Workflows
- **Quality Checks**: Automated code validation and WordPress standards
- **Size Check**: Package size monitoring for pull requests
- **Release**: Automated release creation from version tags

## License

GPL v2 or later

## Author

Silver Assist  
Website: http://silverassist.com/

## Support

For support and feature requests, please contact Silver Assist or open an issue in the project repository.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed version history and changes.
