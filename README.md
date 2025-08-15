# NextJS GraphQL Hooks

A WordPress plugin that creates default GraphQL queries for NextJS sites with extensible type registration through filters.

## Description

NextJS GraphQL Hooks provides essential GraphQL queries and types that are commonly needed when building NextJS sites with WordPress as a headless CMS. The plugin follows modern PHP 8.0+ standards and uses the singleton pattern for efficient resource management.

## Features

- **Default Page Fields**: Automatically adds `elementorContent` and `elementorCSSFile` fields to Page queries (requires Elementor)
- **Elementor Integration**: Provides `elementorLibraryKit` query for global Elementor styles (requires Elementor)
- **Extensible Architecture**: Use filters to register custom GraphQL types and fields
- **Modern PHP 8.0+**: Built with modern PHP features including typed properties, match expressions, and namespaces
- **Singleton Pattern**: Efficient resource management with singleton instances
- **Error Handling**: Comprehensive error logging and graceful fallbacks
- **Auto-Update System**: Automatic updates from GitHub releases
- **GitHub Workflows**: Automated quality checks and release management

## Requirements

- WordPress 6.5+
- PHP 8.0+
- WPGraphQL plugin (automatically managed as dependency)
- Elementor plugin (required for GraphQL queries)

## Compatibility

### WordPress Version Requirements

#### WordPress 6.5+ (Recommended)
- **Full Feature Support**: All plugin features are fully supported
- **Automatic Dependency Management**: WPGraphQL is automatically handled as a plugin dependency
- **Seamless Installation**: WordPress will prompt to install WPGraphQL if not already present
- **No Manual Configuration**: Dependencies are managed automatically by WordPress core

#### WordPress 6.0 - 6.4 (Limited Support)
- **Core Features Available**: All GraphQL functionality works normally
- **Manual Dependency Management**: WPGraphQL must be installed manually
- **Admin Notices**: Plugin will show notices if WPGraphQL is missing
- **Fallback Behavior**: Plugin will not activate core features until WPGraphQL is available

#### WordPress Below 6.0 (Not Supported)
- **Not Recommended**: This version does not meet minimum requirements
- **Security Concerns**: Older WordPress versions may have security vulnerabilities
- **Feature Limitations**: Some modern WordPress features may not be available

### PHP Version Requirements

#### PHP 8.0+ (Required)
- **Modern Language Features**: Plugin uses PHP 8.0+ syntax and features
- **Type Declarations**: Full type safety with union types and typed properties
- **Performance**: Better performance with JIT compilation
- **Security**: Latest security features and improvements

#### PHP 7.4 and Below (Not Supported)
- **Syntax Errors**: Plugin will not load due to PHP 8.0+ syntax
- **Missing Features**: Required language features not available
- **End of Life**: These PHP versions are no longer supported

### Plugin Dependencies

#### WPGraphQL Plugin
- **Required**: Essential for all plugin functionality
- **WordPress 6.5+**: Automatically managed as dependency
- **WordPress 6.0-6.4**: Must be installed manually
- **Minimum Version**: Latest stable version recommended

#### Elementor Plugin
- **Required**: Essential for all GraphQL queries and functionality
- **Features Provided**: 
  - `elementorContent` field - Returns Elementor page content
  - `elementorCSSFile` field - Returns CSS file URLs
  - `elementorLibraryKit` query - Global Elementor styles and kit information
- **Minimum Version**: Latest stable version recommended
- **Note**: Plugin will not function without Elementor as all GraphQL queries depend on Elementor data

### Feature Compatibility Matrix

| Feature | WordPress 6.5+ | WordPress 6.0-6.4 | Notes |
|---------|----------------|-------------------|-------|
| Auto Dependencies | ✅ Full Support | ❌ Manual Required | Core WP feature |
| GraphQL Fields | ✅ Full Support | ✅ Full Support | Requires Elementor |
| Auto Updates | ✅ Full Support | ✅ Full Support | GitHub integration |
| Elementor Integration | ✅ Required | ✅ Required | Core functionality |
| Admin Interface | ✅ Full Support | ✅ Full Support | Settings page |
| Error Handling | ✅ Full Support | ✅ Full Support | Graceful fallbacks |

### Troubleshooting

#### "WPGraphQL Required" Notice
- **WordPress 6.5+**: Check if dependency installation was skipped
- **WordPress 6.0-6.4**: Install WPGraphQL manually
- **Solution**: Ensure WPGraphQL is active and up to date

#### "Elementor Required" Notice
- **All WordPress Versions**: Install and activate Elementor plugin
- **Solution**: Download Elementor from WordPress.org repository or Elementor website
- **Note**: Plugin functionality depends entirely on Elementor being active

#### Plugin Not Loading
- **Check PHP Version**: Must be 8.0 or higher
- **Check WordPress Version**: Must be 6.0 or higher (6.5+ recommended)
- **Check Dependencies**: Ensure both WPGraphQL and Elementor are installed and active

#### GraphQL Fields Missing
- **WPGraphQL Status**: Verify WPGraphQL is active
- **Elementor Status**: Verify Elementor is active and configured
- **Plugin Order**: Ensure NextJS GraphQL Hooks loads after both WPGraphQL and Elementor
- **Cache**: Clear any caching that might affect GraphQL schema

## Download

The plugin is available as a ready-to-install ZIP file from GitHub releases:
- **Latest Version**: 1.0.1
- **Package Size**: ~13KB (compressed)
- **Compatibility**: WordPress 6.5+ with PHP 8.0+
- **Auto-Updates**: Included from GitHub releases
- **Dependencies**: WPGraphQL and Elementor (automatically managed on WordPress 6.5+)

📦 **[Download Latest Release](https://github.com/SilverAssist/nextjs-graphql-hooks/releases/latest)**

## Installation

### Method 1: WordPress Admin Dashboard (Recommended)

1. **Download the Plugin**: Download the `nextjs-graphql-hooks-v1.0.1.zip` file from the [releases page](https://github.com/SilverAssist/nextjs-graphql-hooks/releases)
2. **Access WordPress Admin**: Log in to your WordPress admin dashboard
3. **Navigate to Plugins**: Go to `Plugins` → `Add New`
4. **Upload Plugin**: Click the `Upload Plugin` button at the top of the page
5. **Choose File**: Click `Choose File` and select the downloaded ZIP file
6. **Install**: Click `Install Now` and wait for the upload to complete
7. **Dependencies**: WordPress will automatically prompt to install WPGraphQL and Elementor if needed (WP 6.5+)
8. **Activate**: Click `Activate Plugin` to enable the NextJS GraphQL Hooks plugin

### Method 2: Manual Installation via FTP

1. **Extract the ZIP**: Unzip the downloaded file on your computer
2. **Upload via FTP**: Upload the extracted `nextjs-graphql-hooks` folder to the `/wp-content/plugins/` directory on your server
3. **Activate**: Go to the WordPress admin panel and activate the plugin from the `Plugins` page

### Method 3: WP-CLI Installation

If you have WP-CLI installed, you can also install the plugin using the command line:

```bash
# Download and install the plugin
wp plugin install https://github.com/SilverAssist/nextjs-graphql-hooks/releases/latest/download/nextjs-graphql-hooks-v1.0.1.zip --activate

# Or manually activate after uploading
wp plugin activate nextjs-graphql-hooks
```

### Verification

After installation, you should see:
- **Automatic Dependencies**: WordPress will automatically prompt to install WPGraphQL and Elementor if not present (WordPress 6.5+)
- **GraphQL Fields**: `elementorContent` and `elementorCSSFile` fields available in Page queries
- **Custom Queries**: `elementorLibraryKit` query available in GraphQL
- **Auto-Updates**: Update notifications in WordPress admin when new releases are available
- **Settings Page**: "GraphQL Hooks Updates" under Settings menu for manual update checks

> **Note**: If you're using WordPress 6.5 or later, both WPGraphQL and Elementor dependencies will be automatically managed by WordPress. For older WordPress versions, you'll need to install both plugins manually.

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

The plugin provides a comprehensive filter system that allows you to register custom GraphQL types, root queries, and fields on existing types. This makes it easy to extend the base functionality for your specific needs.

### Filter System Overview

The plugin provides three main filters for extensibility:

- **`nextjs_graphql_hooks_register_types`** - Register custom GraphQL object types
- **`nextjs_graphql_hooks_register_queries`** - Register custom root query fields  
- **`nextjs_graphql_hooks_register_fields`** - Add custom fields to existing GraphQL types

### Complete Implementation Example

Here's a real-world example showing how to implement a complete GraphQL extension in a theme or plugin. This example creates an "Advisor" system with multiple related types:

#### Step 1: Theme/Plugin Integration

```php
<?php
/**
 * Theme GraphQL integration using NextJS GraphQL Hooks
 */

namespace YourTheme\GraphQL;

class Hooks 
{
    private static ?Hooks $instance = null;

    public static function instance(): Hooks 
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() 
    {
        // Require NextJS GraphQL Hooks plugin
        if (!class_exists('NextJSGraphQLHooks\GraphQL_Hooks')) {
            add_action('admin_notices', [$this, 'plugin_dependency_notice']);
            return;
        }

        // Register our custom types through the plugin's filter system
        $this->register_custom_types();
    }

    public function plugin_dependency_notice(): void 
    {
        echo '<div class="notice notice-error"><p>';
        echo __('Your Theme requires the NextJS GraphQL Hooks plugin to be installed and activated.', 'your-textdomain');
        echo '</p></div>';
    }

    private function register_custom_types(): void 
    {
        add_filter('nextjs_graphql_hooks_register_types', [$this, 'add_custom_types']);
        add_filter('nextjs_graphql_hooks_register_queries', [$this, 'add_custom_queries']);  
        add_filter('nextjs_graphql_hooks_register_fields', [$this, 'add_custom_fields']);
    }

    // ... (methods shown below)
}

// Initialize the class
YourTheme\GraphQL\Hooks::instance();
```

#### Step 2: Register Custom Object Types

```php
/**
 * Add custom types to the plugin's type registry
 *
 * @param array $types Existing types array
 * @return array Modified types array
 */
public function add_custom_types(array $types): array 
{
    // Register a nested type for advisor phone information
    $types['AdvisorPhone'] = [
        "description" => __("Advisor Phone Type", "your-textdomain"),
        "fields" => [
            "phone_link" => [
                "type" => "String",
                "description" => __("The phone link of the advisor", "your-textdomain"),
            ],
            "formatted_phone" => [
                "type" => "String", 
                "description" => __("The formatted phone of the advisor", "your-textdomain"),
            ],
        ],
    ];

    // Register a team member type
    $types['AdvisorTeamMember'] = [
        "description" => __("Advisor Team Member Type", "your-textdomain"),
        "fields" => [
            "advisor_name" => [
                "type" => "String",
                "description" => __("The name of the team member", "your-textdomain"),
            ],
            "advisor_position" => [
                "type" => "String",
                "description" => __("The position of the team member", "your-textdomain"),
            ],
            "advisor_image" => [
                "type" => "String",
                "description" => __("The image of the team member", "your-textdomain"),
            ],
        ],
    ];

    // Register the main advisor type with complex fields
    $types['Advisor'] = [
        "description" => __("Advisor Type", "your-textdomain"),
        "fields" => [
            "name" => [
                "type" => "String",
                "description" => __("The name of the advisor", "your-textdomain"),
            ],
            "slug" => [
                "type" => "String", 
                "description" => __("The slug of the advisor", "your-textdomain"),
            ],
            "city" => [
                "type" => "String",
                "description" => __("The city of the advisor", "your-textdomain"),
            ],
            "state" => [
                "type" => "String",
                "description" => __("The state of the advisor", "your-textdomain"),
            ],
            "email" => [
                "type" => "String",
                "description" => __("The email of the advisor", "your-textdomain"),
            ],
            "phone" => [
                "type" => "AdvisorPhone",  // Reference to nested type
                "description" => __("The phone of the advisor", "your-textdomain"),
            ],
            "team_members" => [
                "type" => ["list_of" => "AdvisorTeamMember"],  // Array of nested types
                "description" => __("The team members of the advisor", "your-textdomain"),
            ],
            "additional_states" => [
                "type" => ["list_of" => "String"],  // Array of strings
                "description" => __("The additional states of the advisor", "your-textdomain"),
            ],
        ],
    ];

    return $types;
}
```

#### Step 3: Register Root Query Fields

```php
/**
 * Add custom queries to the plugin's query registry
 *
 * @param array $queries Existing queries array
 * @return array Modified queries array
 */
public function add_custom_queries(array $queries): array 
{
    // Single advisor query with arguments
    $queries['advisor'] = [
        "type" => "Advisor",
        "description" => __("Get a single advisor by slug and name.", "your-textdomain"),
        "args" => [
            "slug" => [
                "type" => "String",
                "description" => __("The location slug of the advisor", "your-textdomain"),
                "required" => true,
            ],
            "name" => [
                "type" => "String", 
                "description" => __("The location name of the advisor", "your-textdomain"),
                "required" => true,
            ],
        ],
        "resolve" => function ($_, $args) {
            return $this->getAdvisor($args);
        }
    ];

    // Multiple advisors query
    $queries['advisors'] = [
        "type" => ["list_of" => "Advisor"],
        "description" => __("Get all advisors.", "your-textdomain"),
        "resolve" => function () {
            return $this->getAdvisors();
        }
    ];

    return $queries;
}
```

#### Step 4: Add Fields to Existing Types

```php
/**
 * Add custom fields to existing GraphQL types
 *
 * @param array $fields Existing fields array
 * @return array Modified fields array
 */
public function add_custom_fields(array $fields): array 
{
    // Add event-specific fields to the Event post type
    $fields['Event'] = [
        "eventDate" => [
            "type" => "String",
            "description" => __("The date of the event", "your-textdomain"),
            "resolve" => function ($post) {
                $meta = get_post_meta($post->databaseId, "event_date", true);
                return !empty($meta) ? $meta : null;
            }
        ],
        "startDate" => [
            "type" => "String",
            "description" => __("The start date of the event", "your-textdomain"),
            "resolve" => function ($post) {
                $meta = get_post_meta($post->databaseId, "start_date", true);
                return !empty($meta) ? $meta : null;
            }
        ],
        "location" => [
            "type" => "String", 
            "description" => __("The location of the event", "your-textdomain"),
            "resolve" => function ($post) {
                $meta = get_post_meta($post->databaseId, "location", true);
                return !empty($meta) ? $meta : null;
            }
        ],
    ];

    return $fields;
}
```

#### Step 5: Implement Resolver Methods

```php
/**
 * Get a single advisor
 *
 * @param array $args Query arguments
 * @return object|WP_Error The advisor object or error
 */
private function getAdvisor($args) 
{
    $slug = isset($args["slug"]) ? sanitize_text_field($args["slug"]) : "";
    $name = isset($args["name"]) ? sanitize_text_field($args["name"]) : "";

    if (empty($slug) && empty($name)) {
        return new \WP_Error("invalid_location", "Invalid location", ["status" => 404]);
    }

    // Custom logic to find advisor by slug/name
    $term_args = [
        "taxonomy" => "category",
        "hide_empty" => false,
        "meta_query" => [
            [
                "key" => "location_data_name",
                "value" => $name,
                "compare" => "=",
            ],
        ],
    ];
    
    $category = get_term_by("slug", $slug, "category") 
        ?: get_terms($term_args)[0] ?? null;

    if (!$category) {
        return new \WP_Error("invalid_location", "Invalid location", ["status" => 404]);
    }

    // Return your custom advisor object
    return new YourAdvisorClass($category);
}

/**
 * Get all advisors
 *
 * @return array Array of advisor objects
 */
private function getAdvisors(): array 
{
    $advisors_categories = $this->getCategories();
    $advisors_data = [];
    
    foreach ($advisors_categories as $category) {
        $advisor = new YourAdvisorClass($category);
        if (!empty($advisor->name) && !empty($advisor->city)) {
            $advisors_data[] = $advisor;
        }
    }
    
    return $advisors_data;
}
```

### GraphQL Query Examples

After implementing the above code, you can query your custom types like this:

#### Single Advisor Query
```graphql
query GetAdvisor($slug: String!, $name: String!) {
  advisor(slug: $slug, name: $name) {
    name
    slug
    city
    state
    email
    phone {
      phone_link
      formatted_phone
    }
    team_members {
      advisor_name
      advisor_position
      advisor_image
    }
    additional_states
  }
}
```

#### All Advisors Query
```graphql
query GetAllAdvisors {
  advisors {
    name
    slug
    city
    state
    email
  }
}
```

#### Event with Custom Fields
```graphql
query GetEvent($id: ID!) {
  event(id: $id) {
    title
    content
    eventDate
    startDate
    location
  }
}
```

### Best Practices

1. **Always check for plugin dependency** before registering filters
2. **Use proper namespacing** to avoid conflicts
3. **Include comprehensive error handling** in resolver functions
4. **Use WordPress sanitization functions** for user input
5. **Follow GraphQL naming conventions** (camelCase for fields)
6. **Provide detailed descriptions** for all types and fields
7. **Test with and without the base plugin** activated

### Quick Implementation for functions.php

For a simpler implementation directly in `functions.php`:

```php
// Add to your theme's functions.php
add_filter('nextjs_graphql_hooks_register_types', function($types) {
    $types['CustomType'] = [
        "description" => "A custom type",
        "fields" => [
            "custom_field" => [
                "type" => "String",
                "description" => "A custom field",
            ],
        ],
    ];
    return $types;
});

add_filter('nextjs_graphql_hooks_register_queries', function($queries) {
    $queries['customQuery'] = [
        "type" => "CustomType",
        "description" => "Get custom data",
        "resolve" => function() {
            return ['custom_field' => 'Custom value'];
        }
    ];
    return $queries;
});
```

This implementation pattern ensures your GraphQL extensions work seamlessly with the NextJS GraphQL Hooks plugin while maintaining clean, maintainable code.

### Available Helper Methods

The plugin provides several helper methods that can be used when extending functionality:

#### `register_custom_object_type($type_name, $config)`
Register a custom GraphQL object type.
- **$type_name**: String name of the GraphQL type
- **$config**: Array with 'description' and 'fields' keys

#### `register_custom_fields($type_registry, $type_name, $fields)`
Add custom fields to an existing GraphQL type.
- **$type_registry**: The WPGraphQL TypeRegistry instance
- **$type_name**: String name of the existing type to extend
- **$fields**: Array of field configurations

#### `register_root_query_field($type_registry, $field_name, $config)`
Register a field in the root query.
- **$type_registry**: The WPGraphQL TypeRegistry instance  
- **$field_name**: String name of the query field
- **$config**: Array with type, description, args, and resolve keys

### Plugin Integration Filters

Instead of using the helper methods directly, the recommended approach is to use the filter system:

#### `nextjs_graphql_hooks_register_types`
Filter to register custom GraphQL object types.
```php
add_filter('nextjs_graphql_hooks_register_types', function($types) {
    $types['YourType'] = [/* type config */];
    return $types;
});
```

#### `nextjs_graphql_hooks_register_queries`  
Filter to register custom root query fields.
```php
add_filter('nextjs_graphql_hooks_register_queries', function($queries) {
    $queries['yourQuery'] = [/* query config */];
    return $queries;
});
```

#### `nextjs_graphql_hooks_register_fields`
Filter to add fields to existing types.
```php
add_filter('nextjs_graphql_hooks_register_fields', function($fields) {
    $fields['ExistingType'] = [/* field configs */];
    return $fields;
});
```

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

### GitHub Workflows
- **Quality Checks**: Automated code validation and WordPress standards
- **Size Check**: Package size monitoring for pull requests
- **Release**: Automated release creation from version tags

## License

Polyform Noncommercial License 1.0.0

## Author

Silver Assist  
Website: http://silverassist.com/

## License

This project is licensed under the [Polyform Noncommercial License 1.0.0](LICENSE) for noncommercial use only.

## Support

For support and feature requests, please contact Silver Assist or open an issue in the project repository.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed version history and changes.

---

**Made with ❤️ by [Silver Assist](https://silverassist.com)**