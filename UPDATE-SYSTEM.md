# NextJS GraphQL Hooks Plugin - Automatic Update System

## 🎉 **Automatic Updates from GitHub (Public Repository)**

Your plugin includes a professional automatic update system that connects directly to your **public** GitHub repository for seamless WordPress updates - no configuration required!

## ✅ **How It Works (Zero Configuration Required)**

- ✅ **No tokens or authentication needed** - public repository access
- ✅ **Automatic Update Checks** - WordPress checks for updates every 12 hours
- ✅ **GitHub Integration** - Downloads updates directly from GitHub releases
- ✅ **WordPress Native Experience** - Updates appear in standard WordPress plugins page
- ✅ **Admin Dashboard** - Dedicated settings page with update status
- ✅ **Manual Update Check** - Force check for updates anytime
- ✅ **Version Caching** - Efficient API usage with 12-hour cache
- ✅ **Error Handling** - Graceful handling of network and API errors

## 📋 How It Works

### 1. **Automatic Detection**
The system automatically detects when running in WordPress admin and initializes the updater.

### 2. **GitHub API Integration**
- Connects to `https://api.github.com/repos/SilverAssist/nextjs-graphql-hooks/releases/latest`
- Retrieves latest version information
- Compares with current installed version

### 3. **WordPress Integration**
- Hooks into WordPress update system
- Shows notifications in standard plugins page
- Provides "Update Now" functionality

### 4. **Download Process**
- Downloads ZIP file from GitHub release assets
- Uses format: `nextjs-graphql-hooks-v{version}.zip`
- Installs through WordPress standard update process

## 🛠️ Files Structure

```
includes/
├── Updater.php                           # Core updater functionality
└── GraphQL_Hooks.php                     # Main plugin functionality

nextjs-graphql-hooks.php                  # Main plugin file with updater integration
```

## 📱 Admin Interface

### Update Settings Page
Navigate to: **Settings → GraphQL Hooks Updates**

Features include:
- **Current Version Display** - Shows installed version
- **Latest Version Check** - Shows available version from GitHub
- **Update Status** - Visual indicators for update availability
- **Manual Update Check** - Button to force version check
- **GitHub Repository Link** - Direct link to source code

### WordPress Plugins Page Integration
The update system integrates seamlessly with the standard WordPress plugins page:
- **Update notifications** appear automatically
- **View Details** shows changelog and release notes
- **Update now** button works like any WordPress plugin
- **Automatic background checks** every 12 hours

## 🔧 Technical Implementation

### Core Updater Class
The `NextJSGraphQLHooks\Updater` class handles all update functionality:

```php
// Automatic initialization in main plugin file
new Updater(__FILE__, "SilverAssist/nextjs-graphql-hooks");
```

### WordPress Hooks Integration
```php
// Check for updates in WordPress transient
add_filter("pre_set_site_transient_update_plugins", [$this, "check_for_update"]);

// Provide plugin information
add_filter("plugins_api", [$this, "plugin_info"], 20, 3);

// Clear cache after updates
add_action("upgrader_process_complete", [$this, "clear_version_cache"], 10, 2);
```

### GitHub API Endpoints
- **Latest Release**: `https://api.github.com/repos/SilverAssist/nextjs-graphql-hooks/releases/latest`
- **All Releases**: `https://api.github.com/repos/SilverAssist/nextjs-graphql-hooks/releases`
- **Download URL**: `https://github.com/SilverAssist/nextjs-graphql-hooks/releases/download/v{version}/nextjs-graphql-hooks-v{version}.zip`

## 📦 Release Package Format

The update system expects release packages in this format:
- **File name**: `nextjs-graphql-hooks-v{version}.zip`
- **GitHub tag**: `v{version}` (e.g., `v1.0.1`)
- **Release asset**: ZIP file attached to GitHub release

## 🎯 Version Management

### Version Comparison
The system uses PHP's `version_compare()` function to determine if updates are available:
```php
if (version_compare($current_version, $latest_version, "<")) {
    // Update available
}
```

### Caching Strategy
- **Version cache**: 12 hours using WordPress transients
- **API rate limiting**: Respectful of GitHub API limits
- **Manual override**: Admin can force immediate version check

### Error Handling
- **Network failures**: Graceful fallback, no WordPress errors
- **API errors**: Logged for debugging, user-friendly messages
- **Invalid responses**: Safe defaults, prevents plugin breaking

## 🚀 User Experience

### For End Users
1. **Install plugin** from ZIP file or WordPress admin
2. **Automatic updates** appear in WordPress admin
3. **Click "Update Now"** just like any WordPress plugin
4. **No configuration needed** - works immediately

### For Administrators
1. **Monitor updates** in Settings → GraphQL Hooks Updates
2. **Force version checks** if needed
3. **View changelog** before updating
4. **Direct access** to GitHub repository

## 🔐 Security Features

### Safe Defaults
- **Public repository only** - no authentication tokens
- **WordPress validation** - uses WordPress update system
- **File integrity** - downloads from official GitHub releases
- **Permission checks** - respects WordPress user capabilities

### Error Prevention
- **Network timeout handling** - prevents hanging requests
- **JSON validation** - validates API responses
- **Version format checking** - ensures valid version numbers
- **File existence validation** - verifies download packages

## 📊 Performance Optimization

### Efficient API Usage
- **12-hour caching** - reduces API calls
- **Conditional requests** - only when necessary
- **Background processing** - doesn't slow down admin
- **Respectful rate limiting** - follows GitHub API guidelines

### WordPress Integration
- **Native update system** - leverages WordPress infrastructure
- **Transient API** - efficient caching mechanism
- **Hook system** - clean integration with WordPress core
- **Admin notices** - standard WordPress notifications

## 🛠️ Developer Information

### Customization Options
The updater can be customized for different repositories:
```php
// Different repository
new Updater(__FILE__, "YourOrg/your-plugin");

// Custom settings
$updater = new Updater(__FILE__, "SilverAssist/nextjs-graphql-hooks");
```

### Debug Information
Enable WordPress debug logging to see updater activity:
```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📞 Support & Troubleshooting

### Common Issues

1. **Updates not appearing**
   - Check internet connection
   - Visit Settings → GraphQL Hooks Updates
   - Click "Check for Updates"

2. **Download failures**
   - Verify GitHub repository is accessible
   - Check WordPress file permissions
   - Review error logs

3. **Version comparison issues**
   - Ensure version numbers follow semantic versioning
   - Check plugin header version format

### Getting Help
- **GitHub Issues**: [Create an issue](https://github.com/SilverAssist/nextjs-graphql-hooks/issues)
- **Documentation**: Review plugin documentation
- **WordPress Forums**: WordPress community support

## 🎉 Benefits Summary

✅ **Professional Experience** - Updates work like WordPress.org plugins  
✅ **Zero Configuration** - No setup required  
✅ **Reliable Updates** - Direct from official releases  
✅ **Admin Dashboard** - Clear update status and controls  
✅ **Automatic Checks** - Never miss important updates  
✅ **Safe Deployment** - Respects WordPress security model  
✅ **Performance Optimized** - Efficient API usage and caching  
✅ **Developer Friendly** - Easy to customize and debug  

The NextJS GraphQL Hooks plugin update system provides a seamless, professional update experience that rivals plugins distributed through the WordPress.org repository!
