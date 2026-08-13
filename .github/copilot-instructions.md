# NextJS GraphQL Hooks — Project Context

WordPress plugin that exposes Elementor page content and global styles as WPGraphQL fields for headless NextJS sites.

## Plugin Info

| Key                 | Value                        |
|---------------------|------------------------------|
| Namespace           | `NextJSGraphQLHooks`         |
| Text Domain         | `nextjs-graphql-hooks`       |
| Version             | 1.3.0                        |
| PHP                 | 8.2+                         |
| WordPress           | 6.5+                         |
| Required dependency | WPGraphQL                    |
| Optional dependency | Elementor                    |

## Architecture

This plugin uses `silverassist/wp-plugin-kernel`'s `AbstractPlugin`/`LoadableInterface` bootstrap
pattern (shared across the SilverAssist WordPress plugin portfolio). `Plugin` extends
`AbstractPlugin`; singleton access (`instance()`), priority-ordered component loading, and
per-component error isolation are inherited, not re-implemented. Bootstraps on the `init` action
(not `plugins_loaded`), since `GraphQL_Hooks::should_load()` depends on `class_exists("WPGraphQL")`,
which isn't reliable until every plugin's `plugins_loaded` callback has already run.

| Class                                        | File                          | Role                                              |
|----------------------------------------------|-------------------------------|----------------------------------------------------|
| `NextJSGraphQLHooks\Plugin`                  | `includes/Plugin.php`         | Root bootstrap: `get_components()`, updater init   |
| `NextJSGraphQLHooks\GraphQL_Hooks`           | `includes/GraphQL_Hooks.php`  | GraphQL type/field registration, Elementor bridge   |
| `NextJSGraphQLHooks\AdminPanel`              | `includes/AdminPanel.php`     | Admin UI, Settings Hub integration, AJAX handlers   |
| `NextJSGraphQLHooks\Updater`                 | `includes/Updater.php`        | Auto-updates from GitHub releases                   |

## GraphQL Schema

### Page Fields (auto-registered)

| Field              | Type     | Description                                  |
|--------------------|----------|----------------------------------------------|
| `elementorContent` | `String` | Elementor HTML content. Arg: `css` (Boolean) |
| `elementorCSSFile` | `String` | Elementor CSS file URL                       |

### Root Query

| Field                | Type                 | Description                          |
|----------------------|----------------------|--------------------------------------|
| `elementorLibraryKit`| `ElementorLibraryKit`| Active kit `kit_id` + `css_file`     |

## Filter System

Three filters allow themes/plugins to extend the GraphQL schema:

| Filter                                     | Purpose                              |
|--------------------------------------------|--------------------------------------|
| `nextjs_graphql_hooks_register_types`      | Register custom GraphQL object types |
| `nextjs_graphql_hooks_register_queries`    | Register custom root query fields    |
| `nextjs_graphql_hooks_register_fields`     | Add fields to existing types         |

## Plugin-Specific Coding Notes

These override or extend the global standards:

- **Double quotes** for all strings (not single quotes).
- **`LoadableInterface` components** — `ClassName::instance()` (`get_instance()` kept as a
  deprecated forwarding alias); implement `init()`, `get_priority()`, `should_load()`.
- **Backslash-prefix** all global WP/PHP functions in namespaced code (`\add_action`, `\register_graphql_object_type`).
- **Elementor methods** must always guard with `\class_exists()` + try/catch.

## Settings Hub Integration

- Register with priority **4** (`\add_action("admin_menu", ..., 4)`) so it runs before the hub (priority 5).
- Always pass `"capability" => "manage_options"` to `$hub->register_plugin()`.
- Check multiple hook suffixes for asset loading: `settings_page_`, `silver-assist_page_`, `toplevel_page_`.
- Action callbacks must **echo** JavaScript, not return it.

## Quick References

| Item               | Path / Command                                |
|--------------------|-----------------------------------------------|
| Main file          | `nextjs-graphql-hooks.php`                    |
| PHPCS config       | `.phpcs.xml.dist`                             |
| Changelog          | `CHANGELOG.md`                                |
| Check versions     | `./scripts/check-versions.sh`                 |
| Update version     | `./scripts/update-version-simple.sh <ver>`    |
| Build release ZIP  | `./scripts/create-release-zip.sh`             |
| Run linter         | `composer phpcs`                              |
| Run tests          | `vendor/bin/phpunit`                          |
