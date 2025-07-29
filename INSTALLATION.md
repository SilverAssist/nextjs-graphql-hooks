# NextJS GraphQL Hooks Plugin - Installation Package

**Version:** 1.0.0  
**Package Size:** 12.84KB (compressed)  
**Created:** July 29, 2025

## Package Contents

This ZIP package contains all necessary files for installing the NextJS GraphQL Hooks WordPress plugin:

- `nextjs-graphql-hooks.php` - Main plugin file
- `includes/GraphQL_Hooks.php` - Core GraphQL functionality  
- `includes/Updater.php` - Auto-update system
- `README.md` - Complete documentation
- `CHANGELOG.md` - Version history
- `LICENSE` - MIT License
- `composer.json` - Composer configuration

## Installation Instructions

### Method 1: WordPress Admin (Recommended)
1. Log in to your WordPress admin dashboard
2. Navigate to **Plugins → Add New**
3. Click **Upload Plugin**
4. Choose the `nextjs-graphql-hooks-v1.0.0.zip` file
5. Click **Install Now**
6. Click **Activate Plugin**

### Method 2: Manual Installation
1. Extract the ZIP file
2. Upload the `nextjs-graphql-hooks` folder to `/wp-content/plugins/`
3. Go to **Plugins** in WordPress admin
4. Find "NextJS GraphQL Hooks" and click **Activate**

### Method 3: FTP Upload
1. Extract the ZIP file on your computer
2. Connect to your site via FTP
3. Upload the entire `nextjs-graphql-hooks` folder to `/wp-content/plugins/`
4. Activate the plugin from WordPress admin

## Requirements

- **WordPress:** 5.0 or higher
- **PHP:** 8.0 or higher  
- **WPGraphQL:** Latest version (required dependency)
- **Elementor:** Optional (for Elementor-related features)

## Post-Installation

1. **Verify Dependencies**: Ensure WPGraphQL plugin is installed and activated
2. **Check Updates**: The plugin includes auto-update functionality from GitHub
3. **Test GraphQL**: Visit `/graphql` endpoint to verify GraphQL is working
4. **Documentation**: See README.md for usage examples and API reference

## Auto-Updates

This plugin includes an auto-update system that checks GitHub releases:
- Updates appear in WordPress admin under **Dashboard → Updates**
- Manual check available at **Settings → GraphQL Hooks Updates**
- Updates are downloaded directly from GitHub releases

## Support

- **GitHub Repository:** https://github.com/SilverAssist/nextjs-graphql-hooks
- **Documentation:** See included README.md
- **Issues:** Report bugs on GitHub Issues
- **Updates:** Automatic via WordPress admin

## Version Information

**Package Details:**
- Uncompressed size: 37.01KB
- Compressed size: 12.84KB  
- Compression ratio: 65.2%
- Total files: 7

**Checksum (SHA256):**
```
bf89cb8767887708129f25964bb830bad46c791fd7fc0c744e0e23361abd5e14
```

---

*This package was created using automated build scripts and includes all necessary files for WordPress plugin installation.*
