# Release Process Guide

## 📋 Complete Release Workflow

This document outlines the complete manual process for creating a new release of the NextJS GraphQL Hooks Plugin.

## 🔄 Pre-Release Checklist

### 1. Version Planning
- [ ] Determine version number (follow [Semantic Versioning](https://semver.org/))
  - **Patch** (x.x.X): Bug fixes, small improvements
  - **Minor** (x.X.x): New features, backward compatible
  - **Major** (X.x.x): Breaking changes, major feature updates

### 2. Code Preparation
- [ ] All features/fixes are completed and tested
- [ ] Code is reviewed and approved
- [ ] All tests pass locally
- [ ] PHP CodeSniffer validation passes: `composer run phpcs`
- [ ] PHP syntax validation: `find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;`

## 📝 Manual Documentation Updates

### 3. Update [CHANGELOG.md](CHANGELOG.md)
**Location**: `/CHANGELOG.md`

Add new version section at the top (after line 7):

```markdown
## [NEW_VERSION] - YYYY-MM-DD

### Added
- New GraphQL fields
- New functionality
- New features

### Enhanced
- Improvements to existing features
- Performance optimizations
- Better error handling

### Fixed
- Bug fixes
- Issue resolutions
- Security improvements

### Changed
- Breaking changes (if any)
- Modified behaviors
- Updated dependencies

### Removed
- Deprecated features
- Removed functionality
```

### 4. Update Version Numbers

**Manual version updates required in:**

1. **Main Plugin File** (`nextjs-graphql-hooks.php`)
   ```php
   * Version: NEW_VERSION
   * @version NEW_VERSION
   ```

2. **Plugin Constants** (`nextjs-graphql-hooks.php`)
   ```php
   define("NEXTJS_GRAPHQL_HOOKS_VERSION", "NEW_VERSION");
   ```

3. **GraphQL Hooks Class** (`includes/GraphQL_Hooks.php`)
   ```php
   * @version NEW_VERSION
   ```

4. **Updater Class** (`includes/Updater.php`)
   ```php
   * @version NEW_VERSION
   ```

5. **README.md** (if version references exist)

## 🚀 Release Creation

### 5. Create Git Tag and Release

#### Option A: Automatic Release (Recommended)
```bash
# Create and push tag (triggers automatic release)
git tag v1.0.1
git push origin v1.0.1
```

#### Option B: Manual Release via GitHub Actions
1. Go to [GitHub Actions](https://github.com/SilverAssist/nextjs-graphql-hooks/actions)
2. Select "Create Release Package" workflow
3. Click "Run workflow"
4. Enter version number (e.g., 1.0.1)
5. Click "Run workflow"

### 6. Verify Release Package

The automated workflow will:
- ✅ Update version numbers in all files
- ✅ Create distribution package (excluding dev files)
- ✅ Generate release notes from CHANGELOG.md
- ✅ Create GitHub release with ZIP file
- ✅ Calculate and display package size

**Excluded from distribution package:**
- `.git*` files and folders
- `.github/` workflow files
- `tmp/` development files
- `vendor/` composer dependencies
- `.phpcs.xml.dist` configuration
- Development documentation

## 📦 Post-Release

### 7. Verify Release
- [ ] Check [GitHub Releases](https://github.com/SilverAssist/nextjs-graphql-hooks/releases) page
- [ ] Download and test ZIP file
- [ ] Verify plugin installation in WordPress
- [ ] Test auto-update functionality
- [ ] Confirm all features work as expected

### 8. Update Documentation (if needed)
- [ ] Update installation instructions
- [ ] Update feature documentation
- [ ] Update screenshots (if UI changes)

## 🔧 Workflow Files Overview

### Automated Workflows

1. **Quality Checks** (`.github/workflows/quality-checks.yml`)
   - Runs on every push and PR
   - PHP CodeSniffer validation
   - Security checks
   - Compatibility checks

2. **Package Size Check** (`.github/workflows/check-size.yml`)
   - Runs on PRs
   - Calculates distribution package size
   - Comments size information on PRs

3. **Release Creation** (`.github/workflows/release.yml`)
   - Runs on tag push or manual trigger
   - Creates distribution package
   - Generates release notes
   - Publishes GitHub release

## 🐛 Troubleshooting

### Common Issues

1. **GitHub Actions Failing**
   - Check [Actions page](https://github.com/SilverAssist/nextjs-graphql-hooks/actions)
   - Review error logs
   - Ensure all required files exist

2. **Version Number Issues**
   - Verify all files have correct version
   - Check tag format: `v1.0.1` (with v prefix)

3. **Package Size Too Large**
   - Review included files in `release.yml`
   - Add exclusions for large unnecessary files

4. **Auto-Update Not Working**
   - Check Updater class configuration
   - Verify GitHub repository permissions
   - Test version checking manually

## 📋 Release Checklist Template

Copy this checklist for each release:

```markdown
## Release v[VERSION] Checklist

### Pre-Release
- [ ] Code complete and tested
- [ ] CHANGELOG.md updated
- [ ] Version numbers updated
- [ ] Quality checks pass

### Release
- [ ] Git tag created: `v[VERSION]`
- [ ] GitHub release created
- [ ] ZIP package generated
- [ ] Release notes published

### Post-Release
- [ ] Release verified and tested
- [ ] Auto-update tested
- [ ] Documentation updated
- [ ] Team notified
```

## 📞 Support

For release process questions or issues:
- Create issue in [GitHub Issues](https://github.com/SilverAssist/nextjs-graphql-hooks/issues)
- Contact the development team
- Review previous release workflow runs for reference
