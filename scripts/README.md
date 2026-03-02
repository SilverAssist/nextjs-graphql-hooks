# Release Automation Documentation

## 🚀 Automated Release System

This system completely automates the release creation process for the NextJS GraphQL Hooks Plugin, including automatic package size calculation.

## 📁 System Files

### GitHub Actions Workflows
- **`.github/workflows/release.yml`** - Main release workflow
- **`.github/workflows/check-size.yml`** - Package size verification for PRs

### Local Scripts
- **`scripts/calculate-size.sh`** - Local package size calculation
- **`scripts/update-version-simple.sh`** - Simplified Perl-based version updating (recommended)
- **`scripts/check-versions.sh`** - Version consistency verification

## 🔄 Automated Workflow

### 1. Local Development

```bash
# Check current package size
./scripts/calculate-size.sh

# The script automatically updates RELEASE-NOTES.md with current size
```

### 2. Pull Requests

When you create a PR, GitHub Actions automatically:
- ✅ Calculates package size
- ✅ Comments on PR with current size
- ✅ Updates comment if you make more changes

### 3. Create Release

#### Option A: Using Git Tags (Recommended)
```bash
# 1. Update CHANGELOG.md with new version changes
# 2. Commit and push changes
git add .
git commit -m "Prepare release v1.0.1"
git push origin main

# 3. Create and push tag
git tag v1.0.1
git push origin v1.0.1

# 4. GitHub Actions automatically:
#    - Updates versions in all files
#    - Calculates package size
#    - Creates distribution ZIP
#    - Updates RELEASE-NOTES.md
#    - Creates GitHub Release
#    - Attaches package ZIP
```

#### Option B: Manual Release from GitHub
1. Go to GitHub → Actions → "Create Release Package"
2. Click "Run workflow"
3. Enter version (e.g., 1.0.1)
4. Click "Run workflow"

## 📦 What the Automation Does

### Automatic Version Updates
The system automatically updates the version in:
- `nextjs-graphql-hooks.php` (plugin header)
- All PHP files (`@version`)

### Size Calculation
- ✅ Excludes development files (`.github/`, `scripts/`, etc.)
- ✅ Creates optimized ZIP for distribution
- ✅ Calculates size in KB with precision
- ✅ Automatically updates `RELEASE-NOTES.md`

### Package Creation
- ✅ Includes only necessary files for distribution
- ✅ Names ZIP with version: `nextjs-graphql-hooks-v1.1.0.zip`
- ✅ Automatically attaches to GitHub Release

### Release Notes Generation
- ✅ Extracts changes from `CHANGELOG.md` for specific version
- ✅ Includes package information (size, installation)
- ✅ Adds installation instructions

## 🛠️ Archivos Incluidos/Excluidos

### ✅ Included in Distribution Package
```
nextjs-graphql-hooks.php
includes/
README.md
CHANGELOG.md
LICENSE
composer.json
.phpcs.xml.dist
```

### ❌ Excluded from Package
```
.git/
.github/
scripts/
node_modules/
*.tmp, *.log
.gitignore
```

## 📊 Useful Local Commands

### Check Current Size
```bash
./scripts/calculate-size.sh
```

### Create Local Package for Testing
```bash
# Create temporary ZIP for testing
zip -r nextjs-graphql-hooks-test.zip . -x "*.git*" ".github/*" "scripts/*" "node_modules/*"

# Check size
ls -lh nextjs-graphql-hooks-test.zip

# Clean up
rm nextjs-graphql-hooks-test.zip
```

### Verify Syntax Before Release
```bash
# PHP
php -l nextjs-graphql-hooks.php
find includes/ -name "*.php" -exec php -l {} \;

# Composer validation
composer validate
```

## 🎯 Automation Benefits

### For the Developer
- 🚀 **1-click release** - Just create tag and everything is automated
- 📊 **Always updated size** - No more manual estimates
- 🔄 **Consistent versions** - Updates all files automatically
- 📋 **Automatic release notes** - Generated from CHANGELOG

### For Users
- 📦 **Optimized package** - Only necessary files
- 📏 **Accurate information** - Real download size
- 🔗 **Direct download** - ZIP attached to GitHub Release
- 📖 **Clear instructions** - Release notes with installation steps

### For Distribution
- 🏪 **Marketplace ready** - Standard package
- 📋 **Complete information** - Technical specifications
- 🔄 **Repeatable process** - Same flow for all versions

## 🔧 Emergency Configuration

If you need to create a release manually without automation:

```bash
# 1. Update versions manually in files
# 2. Create package
mkdir -p dist/nextjs-graphql-hooks-v1.1.0
rsync -av --exclude='.git*' --exclude='.github/' --exclude='scripts/' --exclude='node_modules/' ./ dist/nextjs-graphql-hooks-v1.1.0/
cd dist
zip -r nextjs-graphql-hooks-v1.1.0.zip nextjs-graphql-hooks-v1.1.0/

# 3. Upload manually to GitHub Releases
```

## 📋 Version Management Scripts

### Check Current Versions

```bash
# Display version consistency report across all files
./scripts/check-versions.sh
```

**Output includes:**
- ✅ Main plugin file versions (header, constant, docblock)
- ✅ All PHP files (@version tags)
- ✅ All CSS files (@version tags) (if present)
- ✅ All JavaScript files (@version tags) (if present)
- ✅ Consistency warnings and errors

### Update All Versions

**⚠️ Important: Use the improved script for better reliability**

```bash
# Use the Perl-based updater (recommended for cross-platform compatibility)
./scripts/update-version-simple.sh 1.0.2
```

The `update-version-simple.sh` script uses Perl for more reliable text replacement across different systems.

**What gets updated:**
- Plugin header version in `nextjs-graphql-hooks.php`
- Plugin constant `NEXTJS_GRAPHQL_HOOKS_VERSION`
- All `@version` tags in PHP, CSS, and JavaScript files
- The update scripts themselves

### Version Management Workflow

```bash
# 1. Check current version consistency
./scripts/check-versions.sh

# 2. Update to new version (recommended method)
./scripts/update-version-simple.sh 1.0.2

# 3. Verify all versions were updated
./scripts/check-versions.sh

# 4. Continue with release process
git add .
git commit -m "🔧 Update version to 1.0.2"
```

**The script updates:**
- 📄 Main plugin file (header, constant, @version)
- 📄 All PHP files (@version tags)
- 📄 All CSS files (@version tags) (if present)
- 📄 All JavaScript files (@version tags) (if present)

**Important Notes:**
- ⚠️ Script only updates `@version` tags, not `@since` tags
- ⚠️ New files need `@since` tag set manually
- ✅ Validates semantic version format
- ✅ Shows confirmation before making changes
- ✅ Provides next steps after completion

### Complete Release Workflow

```bash
# 1. Check current version consistency
./scripts/check-versions.sh

# 2. Update to new version
./scripts/update-version-simple.sh 1.0.2

# 3. Review changes
git diff

# 4. Update CHANGELOG.md manually (add new version section)

# 5. Commit version update
git add .
git commit -m "🔧 Update version to 1.0.2"

# 6. Create tag and trigger automated release
git tag v1.0.2
git push origin main
git push origin v1.0.2
```

## 🚨 Troubleshooting

### Version Update Script Issues

### Cross-Platform Compatibility

**Problem:** Version update scripts may behave differently on macOS vs Linux
- **Cause:** Different `sed` implementations between macOS and GNU/Linux
- **Solution:** Use `./scripts/update-version-simple.sh` which uses Perl for consistent behavior
- **Details:** Perl-based text replacement is more reliable across systems

**Symptoms:**
- Only main plugin file gets updated
- PHP files remain at old version
- `check-versions.sh` shows version mismatches

**Fix:**
```bash
# Use the improved script
./scripts/update-version-simple.sh <version>

# Or manual fix for specific files
find includes/ -name "*.php" | xargs sed -i '' 's/@version [0-9]\+\.[0-9]\+\.[0-9]\+/@version 1.0.2/g'
```

### If GitHub Actions Fails
1. Verify tag has format `v1.0.1`
2. Check GitHub Actions permissions
3. Review logs in GitHub → Actions

### If Size is Incorrect
1. Run `./scripts/calculate-size.sh` locally
2. Check excluded files in `.github/workflows/release.yml`
3. Verify there are no unwanted large files

With this system you'll never have to manually calculate package size again! 🎉
