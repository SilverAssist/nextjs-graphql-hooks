# Quick Release Reference

## 🚀 Release Checklist (Copy for each release)

**Release Version**: `v_____`
**Release Date**: `_____`

### Pre-Release Tasks
- [ ] **Determine version number** (patch/minor/major)
- [ ] **Complete all development work**
- [ ] **Test functionality** locally
- [ ] **Run quality checks**: `composer run phpcs`
- [ ] **Check PHP syntax**: `find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;`

### Documentation Updates
- [ ] **Update CHANGELOG.md**
  - Add new version section: `## [X.X.X] - YYYY-MM-DD`
  - Document all changes (Added/Enhanced/Fixed/Changed/Removed)
- [ ] **Update README.md** (if features changed)
  - Version references
  - New feature documentation
  - Installation instructions (if changed)

### Manual Version Updates
- [ ] **Main plugin file** (`nextjs-graphql-hooks.php`):
  ```php
  * Version: X.X.X
  * @version X.X.X
  ```
- [ ] **Plugin constants** (`nextjs-graphql-hooks.php`):
  ```php
  define("NEXTJS_GRAPHQL_HOOKS_VERSION", "X.X.X");
  ```
- [ ] **GraphQL Hooks class** (`includes/GraphQL_Hooks.php`):
  ```php
  * @version X.X.X
  ```
- [ ] **Updater class** (`includes/Updater.php`):
  ```php
  * @version X.X.X
  ```

### Release Execution
- [ ] **Commit documentation**: 
  ```bash
  git add CHANGELOG.md README.md
  git commit -m "📚 Update documentation for vX.X.X release"
  git push origin main
  ```
- [ ] **Create and push tag**:
  ```bash
  git tag vX.X.X
  git push origin vX.X.X
  ```

### Post-Release Verification
- [ ] **Monitor GitHub Actions**: [Actions](https://github.com/SilverAssist/nextjs-graphql-hooks/actions)
- [ ] **Verify release created**: [Releases](https://github.com/SilverAssist/nextjs-graphql-hooks/releases)
- [ ] **Download and test** the ZIP file
- [ ] **Test installation** in WordPress
- [ ] **Test auto-update** functionality

---

## 📋 Common Version Patterns

### Patch Release (x.x.X)
- Bug fixes
- Security updates
- Small improvements
- Documentation updates

### Minor Release (x.X.x)  
- New features
- New GraphQL fields
- Enhanced functionality
- Backward compatible changes

### Major Release (X.x.x)
- Breaking changes
- Major feature additions
- Architecture changes
- WordPress/PHP version requirement updates

---

## 🔧 Quick Commands

### Version Check
```bash
# Check current version in files
grep -r "Version:" nextjs-graphql-hooks.php
grep -r "NEXTJS_GRAPHQL_HOOKS_VERSION" nextjs-graphql-hooks.php
```

### Quality Assurance
```bash
# Run all quality checks
composer validate --strict
composer run phpcs
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;
```

### Git Operations
```bash
# Quick release commands
git add .
git commit -m "🚀 Release vX.X.X"
git push origin main
git tag vX.X.X
git push origin vX.X.X
```

### Verification
```bash
# Check if release was created
curl -s https://api.github.com/repos/SilverAssist/nextjs-graphql-hooks/releases/latest | grep tag_name

# Download latest release
wget https://github.com/SilverAssist/nextjs-graphql-hooks/releases/latest/download/nextjs-graphql-hooks-vX.X.X.zip
```

---

## 🎯 Manual Release (if automation fails)

### Via GitHub Actions
1. Go to [GitHub Actions](https://github.com/SilverAssist/nextjs-graphql-hooks/actions)
2. Select "Create Release Package" workflow
3. Click "Run workflow"
4. Enter version number (e.g., 1.0.1)
5. Click "Run workflow"

### Via GitHub Web Interface
1. Go to [Releases](https://github.com/SilverAssist/nextjs-graphql-hooks/releases)
2. Click "Create a new release"
3. Choose tag: `vX.X.X`
4. Release title: `NextJS GraphQL Hooks vX.X.X`
5. Copy description from CHANGELOG.md
6. Upload ZIP file manually (if needed)
7. Publish release

---

## 🐛 Troubleshooting

### GitHub Actions Failed
- Check the [Actions page](https://github.com/SilverAssist/nextjs-graphql-hooks/actions)
- Review error logs
- Retry the workflow
- Create manual release if needed

### Version Mismatch
- Verify all files have correct version
- Check tag format: `vX.X.X` (with v prefix)
- Ensure no typos in version numbers

### ZIP Package Issues
- Check file exclusions in `release.yml`
- Verify all required files are included
- Test installation manually

---

## 📞 Emergency Release Process

If urgent fixes are needed:

1. **Hotfix branch** (optional):
   ```bash
   git checkout -b hotfix/vX.X.X
   ```

2. **Quick fix and test**
3. **Update version numbers** manually
4. **Create release**:
   ```bash
   git commit -am "🔥 Hotfix vX.X.X"
   git push origin main  # or hotfix branch
   git tag vX.X.X
   git push origin vX.X.X
   ```

5. **Monitor** automated release creation
6. **Verify** and **communicate** to users

---

## ✅ Success Indicators

Release is successful when:
- ✅ GitHub release is created with ZIP file
- ✅ ZIP file contains correct files and version
- ✅ Plugin installs correctly in WordPress
- ✅ Auto-update system detects new version
- ✅ All functionality works as expected
- ✅ No errors in WordPress admin
