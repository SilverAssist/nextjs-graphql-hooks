# NextJS GraphQL Hooks — Project Context

WordPress plugin that exposes Elementor page content and global styles as WPGraphQL fields for headless NextJS sites.

## Plugin Info

| Key                 | Value                        |
|---------------------|------------------------------|
| Namespace           | `NextJSGraphQLHooks`         |
| Text Domain         | `nextjs-graphql-hooks`       |
| Version             | 1.2.0                        |
| PHP                 | 8.0+                         |
| WordPress           | 6.5+                         |
| Required dependency | WPGraphQL                    |
| Optional dependency | Elementor                    |

## Architecture

This plugin does **NOT** use LoadableInterface — it uses a **singleton pattern** (`get_instance()`) in all classes.

| Class                                        | File                          | Role                                              |
|----------------------------------------------|-------------------------------|----------------------------------------------------|
| `NextJSGraphQLHooks\NextJS_GraphQL_Hooks`    | `nextjs-graphql-hooks.php`    | Main plugin, dependency checks, initialization     |
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
- **Singleton pattern** — no LoadableInterface, use `ClassName::get_instance()`.
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
