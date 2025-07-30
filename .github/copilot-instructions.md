# Copilot Instructions for NextJS GraphQL Hooks Plugin

## Plugin Overview

NextJS GraphQL Hooks is a WordPress plugin that provides essential GraphQL queries for NextJS sites using WordPress as a headless CMS. The plugin is built with modern PHP 8.0+ features, follows WordPress coding standards, and uses a singleton pattern for efficient resource management.

## Core Architecture

### Main Components

1. **Main Plugin Class**: `NextJSGraphQLHooks\NextJS_GraphQL_Hooks`
   - Singleton pattern implementation
   - Handles plugin initialization and dependency checks
   - Manages WPGraphQL dependency validation
   - Initializes auto-updater and GraphQL hooks
   - Located in: `nextjs-graphql-hooks.php`

2. **GraphQL Hooks Class**: `NextJSGraphQLHooks\GraphQL_Hooks`
   - Singleton pattern implementation
   - Manages GraphQL type and field registration
   - Provides extensible filter system for custom types
   - Handles Elementor integration with error handling
   - Located in: `includes/GraphQL_Hooks.php`

3. **Auto-Update System**: `NextJSGraphQLHooks\Updater`
   - Handles automatic updates from GitHub releases
   - Integrates with WordPress update notifications
   - Manages version checking and plugin info display
   - Located in: `includes/Updater.php`

### Key Features

- **Default Page Fields**: Automatically adds `elementorContent` and `elementorCSSFile` fields to Page queries
- **Elementor Integration**: Provides `elementorLibraryKit` root query for global Elementor styles
- **Extensible Filter System**: Three filter hooks for custom types, queries, and fields
- **Auto-Updates**: Automatic plugin updates from GitHub releases with WordPress admin integration
- **GitHub Workflows**: CI/CD pipeline with quality checks, size monitoring, and automated releases
- **Error Handling**: Comprehensive error logging and graceful fallbacks for missing dependencies
- **Modern PHP**: Uses PHP 8.0+ features including typed properties, constructor property promotion, and namespaces
- **WordPress 6.5+ Compatibility**: Full support for WordPress plugin dependency system

## Coding Standards

### PHP Coding Guidelines

```php
// ✅ Correct - Use double quotes for strings
$message = "Hello World";

// ❌ Incorrect - Single quotes (only use for literal strings)
$message = 'Hello World';

// ✅ Correct - Short array syntax
$array = ["item1", "item2"];

// ❌ Incorrect - Old array syntax
$array = array("item1", "item2");

// ✅ Correct - Namespace usage
namespace NextJSGraphQLHooks;

// ✅ Correct - Global function calls in namespace (backslash prefix)
\add_action("init", [$this, "method"]);
\register_graphql_object_type($type_name, $config);

// ✅ Correct - Typed properties and return types
private static ?NextJS_GraphQL_Hooks $instance = null;

// ✅ Correct - Constructor parameter types and return type declarations
public function get_instance(): NextJS_GraphQL_Hooks
{
    if (!isset(self::$instance)) {
        self::$instance = new self();
    }
    return self::$instance;
}

// ✅ Correct - Named parameters (PHP 8.0+)
$type_registry->register_fields(
    type_name: "Page",
    fields: $fields_array
);

// ✅ Correct - Error handling with try-catch
try {
    $elementor = Elementor::instance();
    $document = $elementor->documents->get($post->ID);
    return $document ? $document->get_content() : "";
} catch (\Exception $e) {
    \error_log("NextJS GraphQL Hooks - Error: " . $e->getMessage());
    return "";
}
```

### WordPress Integration

```php
// ✅ Correct - WordPress hooks in namespaced context
\add_action("graphql_register_types", [$this, "register_graphql_types"]);

// ✅ Correct - Internationalization with text domain
\__("Text to translate", "nextjs-graphql-hooks");

// ✅ Correct - WPGraphQL type registration
\register_graphql_object_type("CustomType", [
    "description" => \__("Custom type description", "nextjs-graphql-hooks"),
    "fields" => $fields
]);

// ✅ Correct - Plugin dependency checking
if (!class_exists("WPGraphQL")) {
    \add_action("admin_notices", [$this, "wpgraphql_missing_notice"]);
    return;
}
```

## File Structure

```
nextjs-graphql-hooks/
├── nextjs-graphql-hooks.php          # Main plugin file
├── includes/
│   ├── GraphQL_Hooks.php             # Core GraphQL functionality
│   └── Updater.php                   # Auto-update system
├── .github/
│   ├── copilot-instructions.md       # This file - Copilot guidelines
│   └── workflows/
│       ├── quality-checks.yml        # CI/CD quality checks
│       ├── check-size.yml            # Package size monitoring
│       └── release.yml               # Automated releases
├── scripts/
│   ├── check-versions.sh             # Version consistency verification
│   ├── create-release-zip.sh         # Release package creation
│   ├── update-version.sh             # Version update automation
│   ├── update-version-simple.sh      # Simple version update (Perl-based)
│   └── README.md                     # Scripts documentation
├── composer.json                     # Composer configuration
├── .phpcs.xml.dist                   # PHP CodeSniffer configuration
├── CHANGELOG.md                      # Version history
├── LICENSE                           # GPL v2 license
├── README.md                         # Plugin documentation
├── RELEASE-PROCESS.md                # Release workflow guide
├── UPDATE-SYSTEM.md                  # Auto-update documentation
└── QUICK-RELEASE.md                  # Emergency release procedures
```

## Development Guidelines

### Adding New Features

1. **Follow Singleton Pattern**: Use `get_instance()` for class instantiation
2. **Use Filter System**: Implement the three available filter hooks for extensibility
3. **Error Handling**: Always include try-catch blocks for external API calls
4. **Type Safety**: Use PHP 8+ typed properties and return types
5. **Auto-Updates**: Consider update compatibility when changing core functionality

### Filter System

The plugin provides three filter hooks for extensibility:

1. **`nextjs_graphql_hooks_register_types`**: Register custom GraphQL object types
2. **`nextjs_graphql_hooks_register_queries`**: Register custom root query fields
3. **`nextjs_graphql_hooks_register_fields`**: Register custom fields on existing types

### Example: Adding New GraphQL Features

```php
// The register_graphql_types method structure
public function register_graphql_types(TypeRegistry $type_registry): void
{
    // Register core page fields
    $this->register_page_fields($type_registry);
    
    // Register Elementor library kit field
    $this->register_elementor_library_kit_field($type_registry);
    
    // Allow extensions via filter system
    $this->register_extension_types($type_registry);
}

// Extension types registration through filters
private function register_extension_types(TypeRegistry $type_registry): void
{
    // Allow themes/plugins to register custom object types
    $custom_types = \apply_filters('nextjs_graphql_hooks_register_types', []);
    foreach ($custom_types as $type_name => $type_config) {
        $this->register_custom_object_type($type_name, $type_config);
    }

    // Allow themes/plugins to register custom root query fields
    $custom_queries = \apply_filters('nextjs_graphql_hooks_register_queries', []);
    foreach ($custom_queries as $field_name => $field_config) {
        $this->register_root_query_field($type_registry, $field_name, $field_config);
    }

    // Allow themes/plugins to register custom fields on existing types
    $custom_fields = \apply_filters('nextjs_graphql_hooks_register_fields', []);
    foreach ($custom_fields as $type_name => $fields) {
        $this->register_custom_fields($type_registry, $type_name, $fields);
    }
}
```

### Example: Using the Filter System

```php
// In functions.php or another plugin

// 1. Register custom object types
\add_filter('nextjs_graphql_hooks_register_types', function ($types) {
    $types['CustomType'] = [
        'description' => \__('Custom type description', 'nextjs-graphql-hooks'),
        'fields' => [
            'customField' => [
                'type' => 'String',
                'description' => \__('Custom field description', 'nextjs-graphql-hooks'),
            ],
        ],
    ];
    return $types;
});

// 2. Register custom root query fields
\add_filter('nextjs_graphql_hooks_register_queries', function ($queries) {
    $queries['customQuery'] = [
        'type' => 'String',
        'description' => \__('Custom query description', 'nextjs-graphql-hooks'),
        'resolve' => function () {
            return 'Custom query result';
        }
    ];
    return $queries;
});

// 3. Register custom fields on existing types
\add_filter('nextjs_graphql_hooks_register_fields', function ($fields) {
    $fields['Post'] = [
        'customPostField' => [
            'type' => 'String',
            'description' => \__('Custom post field', 'nextjs-graphql-hooks'),
            'resolve' => function ($post) {
                return \get_post_meta($post->databaseId, 'custom_meta', true);
            }
        ]
    ];
    return $fields;
});
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
   - Use for top-level queries like `customQuery` or `settings`

### Core Field Implementations

#### Page Fields

The plugin automatically registers two fields on the Page type:

1. **`elementorContent`**: Returns Elementor page content
   - Type: `String`
   - Args: `css` (Boolean) - Include CSS inline (default: false)
   - Returns: HTML content from Elementor or empty string

2. **`elementorCSSFile`**: Returns Elementor CSS file URL
   - Type: `String`
   - Returns: CSS file URL or empty string

#### Root Query Fields

The plugin registers one root query field:

1. **`elementorLibraryKit`**: Returns Elementor library kit information
   - Type: `ElementorLibraryKit`
   - Fields: `kit_id` (String), `css_file` (String)
   - Returns: Active kit information or empty values

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
```

## Dependencies

### Required

- **WordPress**: 6.5+ (recommended), 6.0+ (limited support)
- **PHP**: 8.0+
- **WPGraphQL**: Latest version (automatically managed as dependency in WordPress 6.5+)

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
// Using the filter system
\add_filter('nextjs_graphql_hooks_register_fields', function ($fields) {
    $fields['Post'] = [
        'customMeta' => [
            'type' => 'String',
            'description' => \__('Custom meta field', 'nextjs-graphql-hooks'),
            'resolve' => function ($post) {
                $meta = \get_post_meta($post->databaseId, 'custom_meta_key', true);
                return !empty($meta) ? $meta : null;
            }
        ]
    ];
    return $fields;
});
```

### Creating List Types

```php
// Using the filter system
\add_filter('nextjs_graphql_hooks_register_types', function ($types) {
    $types['ListItem'] = [
        'fields' => [
            'title' => ['type' => 'String'],
            'value' => ['type' => 'String'],
        ],
    ];
    return $types;
});

// Use in another type
\add_filter('nextjs_graphql_hooks_register_fields', function ($fields) {
    $fields['Page'] = [
        'items' => [
            'type' => ['list_of' => 'ListItem'],
            'description' => \__('List of items', 'nextjs-graphql-hooks'),
        ]
    ];
    return $fields;
});
```

## Security Considerations

1. **Input Sanitization**: Always sanitize GraphQL arguments
2. **Error Messages**: Don't expose sensitive information in error messages
3. **Capability Checks**: Implement proper WordPress capability checks when needed
4. **Data Validation**: Validate data before processing

## Release & Update System

### GitHub Workflows

1. **quality-checks.yml**: Runs on push/PR
   - Composer validation and installation
   - PHP CodeSniffer with WordPress standards
   - Security vulnerability scanning
   - Tests multiple PHP versions (8.0, 8.1, 8.2, 8.3)
   - Tests multiple WordPress versions (6.5, 6.6, latest)

2. **check-size.yml**: Runs on pull requests
   - Monitors distribution package size
   - Comments on PRs with size changes
   - Prevents bloated releases

3. **release.yml**: Runs on version tags
   - Updates version numbers in files
   - Creates distribution package
   - Publishes GitHub release with changelog

### Local Scripts

1. **`check-versions.sh`**: Version consistency verification
   - Displays comprehensive version report across all plugin files
   - Shows main plugin file versions (header, constant, docblock)
   - Lists all PHP files with @version tags
   - Identifies version mismatches and missing version tags
   - Provides clear colored output for easy reading

2. **`update-version.sh`**: Comprehensive version update automation
   - Updates main plugin file (header, constant, @version)
   - Updates all PHP files (@version tags)
   - Updates CSS and JavaScript files (@version tags) if present
   - Validates semantic version format
   - Provides confirmation before making changes
   - May have compatibility issues with macOS sed

3. **`update-version-simple.sh`**: Perl-based version updater (recommended)
   - More reliable than sed-based version on macOS
   - Same functionality as update-version.sh but with better cross-platform compatibility
   - Uses Perl for text replacement instead of sed
   - Handles optional directories gracefully (assets, blocks)

4. **`create-release-zip.sh`**: Release package creation
   - Creates properly structured ZIP file for WordPress plugin distribution
   - Excludes development files (.git, .github, scripts, etc.)
   - Names ZIP with version but internal folder remains "nextjs-graphql-hooks"
   - Automatically detects version from main plugin file
   - Provides size information and file count

### Auto-Update System

- Uses GitHub API to check for new releases
- Integrates with WordPress update notifications
- Handles plugin updates through admin interface
- Maintains compatibility with WordPress update hooks

### Version Management

- Uses semantic versioning (MAJOR.MINOR.PATCH)
- Version synchronization across multiple files
- Automatic changelog parsing for release notes
- Emergency release procedures documented

#### Version Management Workflow

```bash
# 1. Check current version consistency
./scripts/check-versions.sh

# 2. Update to new version (recommended method - more reliable on macOS)
./scripts/update-version-simple.sh 1.0.2

# Alternative: Original sed-based updater (may have macOS compatibility issues)
./scripts/update-version.sh 1.0.2

# 3. Verify all versions were updated
./scripts/check-versions.sh

# 4. Create release package for testing
./scripts/create-release-zip.sh

# 5. Continue with release process
git add .
git commit -m "🔧 Update version to 1.0.2"
git tag v1.0.2
git push origin main && git push origin v1.0.2
```

#### Script Differences

**`update-version-simple.sh` (Recommended)**
- Uses Perl for text replacement (more reliable cross-platform)
- Better error handling for missing directories
- Handles optional asset directories gracefully
- More consistent behavior on macOS and Linux

**`update-version.sh` (Alternative)**
- Uses sed for text replacement
- May have compatibility issues with macOS sed
- More verbose output and backup creation
- Comprehensive file validation

## Future Enhancements

### Planned Features

1. **Translation Support**: Complete internationalization
2. **Admin Interface**: Settings page for configuration
3. **Query Caching**: Implement caching for expensive queries
4. **Performance Monitoring**: Add query performance tracking
5. **Update Rollback**: Ability to rollback failed updates

### Extensibility Points

1. **Custom Resolvers**: Allow custom resolver registration
2. **Field Validation**: Add field validation hooks
3. **Query Optimization**: Implement query optimization filters
4. **Schema Customization**: Allow schema modification hooks
5. **Update Hooks**: Custom hooks for pre/post update actions

## When Making Changes

### Code Edits

1. **Main Plugin File** (`nextjs-graphql-hooks.php`):
   - Contains plugin header and main class
   - Handles dependency checks and initialization
   - Uses singleton pattern with `get_instance()`

2. **GraphQL Hooks** (`includes/GraphQL_Hooks.php`):
   - Core GraphQL functionality and type registration
   - Three distinct sections: Page fields, Elementor kit, and extensions
   - All Elementor methods include class existence checks and error handling

3. **Auto-Updater** (`includes/Updater.php`):
   - Handles GitHub API interactions and WordPress update hooks
   - Integrates with WordPress admin update notifications
   - Manages version checking and plugin info display

### Testing

- Run `composer phpcs` before committing
- Check WordPress 6.5+ and PHP 8.0+ compatibility
- Test with and without Elementor plugin active
- Verify WPGraphQL dependency handling

### Deployment

- Follow semantic versioning for releases
- Update `CHANGELOG.md` with release notes
- Use GitHub releases for distribution
- Auto-updater handles plugin updates for users

### Documentation

- Update this file when adding major features
- Include examples for new filter implementations
- Document breaking changes in changelog
- Update README.md for public-facing changes

## Best Practices

1. **Always use the filter system** for extensions
2. **Include comprehensive error handling**
3. **Follow WordPress coding standards**
4. **Use proper text domains for translations**
5. **Document all public methods and hooks**
6. **Test with both required and optional dependencies**
7. **Consider update compatibility** when making changes
8. **Use semantic versioning** for all releases
