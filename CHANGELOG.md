# Changelog

All notable changes to the NextJS GraphQL Hooks Plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-07-29

### Added
- Initial release of NextJS GraphQL Hooks plugin
- Default Page fields for Elementor integration:
  - `elementorContent` field with optional CSS inline parameter
  - `elementorCSSFile` field for CSS file URL
- Elementor Library Kit query (`elementorLibraryKit`) with:
  - Kit ID retrieval
  - CSS file URL for global styles
- Extensible filter system using `nextjs_graphql_hooks_register_types` action hook
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
  - WPGraphQL dependency checking
  - Plugin activation/deactivation hooks
  - Admin notices for missing dependencies
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

### Technical Details
- Requires WordPress 5.0+
- Requires PHP 8.0+
- Requires WPGraphQL plugin
- Optional Elementor integration
- GPL v2 or later license
- Text domain: `nextjs-graphql-hooks`
- Author: Silver Assist
