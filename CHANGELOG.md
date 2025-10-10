# Changelog

All notable changes to the NextJS GraphQL Hooks Plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.4] - 2025-10-10

### ✨ New Features
- **Settings Hub Integration**: Added support for `silverassist/wp-settings-hub` package
  - Centralized admin menu under "Silver Assist" menu
  - Graceful fallback to standalone settings page when Settings Hub unavailable
  - Comprehensive admin panel with plugin status and documentation
- **Admin Interface**: New admin panel with multiple sections
  - Plugin status display (version, dependencies)
  - Available GraphQL queries documentation
  - Extensibility guide with filter examples
  - Documentation links and resources
- **Update Checking**: Integrated update check button
  - One-click update checking from admin panel
  - WordPress-style admin notifications
  - Auto-redirect to plugins page when update available
- **Code Examples**: Interactive code blocks
  - Click-to-copy functionality for all code examples
  - Syntax highlighting for better readability
  - GraphQL query examples and filter usage

### 🎨 Assets
- **Admin CSS**: Modern, responsive admin interface styles
  - CSS custom properties for easy theming
  - Status badges and visual indicators
  - Mobile-responsive design
  - WordPress-compliant styling
- **Admin JavaScript**: Enhanced user experience
  - Copy-to-clipboard functionality
  - Smooth scrolling
  - WordPress admin notice system
  - Update checking via AJAX

### 🔧 Enhanced
- **Security**: Comprehensive security implementation
  - Nonce verification for all AJAX requests
  - Capability checking for admin access
  - Sanitized output and escaped HTML
  - Error logging without exposing sensitive data
- **Compatibility**: Multiple hook suffix support
  - Works in standalone mode
  - Works with Settings Hub
  - Works with custom menu configurations

### 📚 Documentation
- **Centralized Documentation**: All documentation consolidated in README.md
  - Admin panel usage guide
  - Settings Hub integration details
  - Troubleshooting and best practices
  - No separate MD files - all in README and Copilot instructions
- **Updated Copilot Instructions**: Added rule to prevent creating additional MD files
  - All technical documentation in copilot-instructions.md
  - All user documentation in README.md
  - Maintains clean repository structure

### 🛠️ Technical
- **Dependencies**: Added `silverassist/wp-settings-hub` ^1.1
- **Files Added**:
  - `includes/AdminPanel.php` - Main admin panel class
  - `assets/css/admin.css` - Admin interface styles
  - `assets/js/admin.js` - Admin functionality
  - `assets/js/update-check.js` - Update checking
  - `SETTINGS_HUB_INTEGRATION.md` - Integration documentation
  - `assets/README.md` - Assets documentation
- **Hook Priority**: Registers at priority 4 to ensure proper Settings Hub integration
- **Singleton Pattern**: AdminPanel uses singleton pattern for consistency
- **AJAX Handlers**: Secure AJAX endpoints for update checking

## [1.0.3] - 2025-08-12

### 🔧 Enhanced
- **Internationalization Support**: Added `text_domain` configuration to updater for proper i18n support
- **Updated Package Dependencies**: Upgraded `silverassist/wp-github-updater` to v1.1.0 with enhanced translation capabilities
- **Improved Update Messages**: All updater messages now use plugin's text domain for consistent translations

### 🛠️ Technical
- Enhanced updater configuration with `text_domain: "nextjs-graphql-hooks"`
- Leverages new translation wrapper methods from wp-github-updater v1.1.0
- Improved HTTP header management for GitHub API interactions

## [1.0.2] - 2025-08-07

### 🚀 Major Updates
- **Modularized Updater System**: Integrated reusable `silverassist/wp-github-updater` package
- **Optimized Build Process**: Enhanced release script with production-only dependencies
- **Improved CI/CD**: Updated GitHub Actions workflow with Composer support

### ✨ Added
- Composer autoloader integration in main plugin file
- Production dependency management in build script
- Optimized vendor directory packaging (only necessary files)
- Automatic development dependency restoration after build

### 🔧 Changed
- **BREAKING**: Refactored `Updater.php` class to extend `SilverAssist\WpGithubUpdater\Updater`
- Reduced Updater class from ~200 lines to ~35 lines (-82% code reduction)
- Updated `create-release-zip.sh` script with Composer dependency handling
- Enhanced GitHub Actions workflow with PHP setup and dependency installation

### 🐛 Fixed
- Resolved missing dependencies in CI/CD pipeline
- Fixed release package size optimization (now includes only production dependencies)
- Improved error handling in build scripts

### 📚 Technical Details
- **Dependencies**: Added `silverassist/wp-github-updater:^1.0`
- **Build Size**: Optimized from ~50KB to ~156KB (includes vendor dependencies)
- **Maintainability**: Centralized update logic in external package
- **Compatibility**: Maintains full backward compatibility

### 🔄 Migration Notes
- Existing installations will automatically use the new updater system
- No manual intervention required for end users
- Developers can now reuse the same updater package across multiple plugins

## [1.0.1] - 2025-07-30

### Updated
- Updated Copilot instructions to reflect current plugin architecture and scripts structure
- Improved version management scripts documentation
- Enhanced filter system documentation with comprehensive examples
- Updated file structure documentation in Copilot instructions

### Fixed
- Corrected script references in documentation to match actual file structure
- Updated version management workflow with proper script recommendations

## [1.0.0] - 2025-07-29

### Added
- Initial release of NextJS GraphQL Hooks plugin
- **WordPress 6.5+ Plugin Dependencies**: Automatic WPGraphQL dependency management using WordPress native plugin dependencies feature
- Default Page fields for Elementor integration:
  - `elementorContent` field with optional CSS inline parameter
  - `elementorCSSFile` field for CSS file URL
- Elementor Library Kit query (`elementorLibraryKit`) with:
  - Kit ID retrieval
  - CSS file URL for global styles
- Extensible filter system using `nextjs_graphql_hooks_register_types` action hook
- Auto-update system with GitHub releases integration
- Modern PHP 8.0+ codebase with:
  - Namespace organization (`NextJSGraphQLHooks`)
  - Singleton pattern implementation
  - Typed properties with nullable types
  - Return type declarations
  - Modern array syntax and double-quoted strings
- Comprehensive error handling:
  - Graceful fallbacks when Elementor is not available
  - Error logging for debugging purposes
  - Empty string returns instead of exceptions
- Helper methods for extensibility:
  - `register_custom_object_type()` for custom GraphQL types
  - `register_custom_fields()` for extending existing types
  - `register_root_query_field()` for root query fields
- WordPress integration features:
  - **WPGraphQL automatic dependency management** (WordPress 6.5+)
  - Plugin activation/deactivation hooks
  - Admin notices for missing dependencies (legacy WordPress support)
  - Auto-update system with GitHub releases
- GitHub Actions workflows for:
  - Automated quality checks with PHP 8.0+ and WordPress 6.5+ compatibility testing
  - Package size monitoring
  - Automated release creation
- Example implementation file showing how to extend the plugin
- Comprehensive documentation with:
  - Installation instructions
  - Usage examples
  - Extension guidelines
  - Development standards

### Security
- Input sanitization for GraphQL arguments
- Proper error message handling to prevent sensitive information exposure
- WordPress capability integration ready for future enhancements

### Requirements
- **WordPress 6.5+** (for automatic plugin dependency management)
- **PHP 8.0+** (modern syntax and features)
- **WPGraphQL plugin** (automatically managed as dependency in WP 6.5+)

### Compatibility Notes
- For WordPress versions below 6.5, WPGraphQL must be installed manually
- Automatic plugin dependency management requires WordPress 6.5 or later
- All features fully supported on WordPress 6.5+ with automatic dependency resolution

### Technical Details
- Requires WordPress 5.0+
- Requires PHP 8.0+
- Requires WPGraphQL plugin
- Optional Elementor integration
- Polyform Noncommercial License 1.0.0
- Text domain: `nextjs-graphql-hooks`
- Author: Silver Assist
