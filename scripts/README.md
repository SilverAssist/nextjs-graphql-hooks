# Scripts Directory

Collection of bash scripts for NextJS GraphQL Hooks plugin development and release management.

## Available Scripts

### 📊 `calculate-size.sh`
Calculates the distribution package size for the plugin.

**Usage:**
```bash
./scripts/calculate-size.sh
```

**Features:**
- Creates distribution package in `./tmp/` directory
- Shows individual file sizes and total package size
- Calculates compression ratio
- Provides size warnings (>2MB warning, >5MB error)
- Compatible with GitHub Actions (outputs environment variables)

**Output:**
- Package ZIP file in `./tmp/nextjs-graphql-hooks.zip`
- Size information in human-readable format
- GitHub Actions compatible environment variables

---

### 🔍 `check-versions.sh`
Verifies version consistency across all plugin files.

**Usage:**
```bash
./scripts/check-versions.sh
```

**Features:**
- Validates semantic versioning format
- Checks version consistency across files:
  - `nextjs-graphql-hooks.php` (main plugin file)
  - `README.md` (version badges/references)
  - `composer.json` (version field)
  - `CHANGELOG.md` (latest version entry)
- Validates Git tag existence
- Provides detailed error reporting

**Exit Codes:**
- `0`: All checks passed
- `1`: Version issues found

---

### 🚀 `update-version.sh` (Complete)
Comprehensive version update script with validation and backup.

**Usage:**
```bash
./scripts/update-version.sh <new_version> [options]
```

**Options:**
- `--force`: Force update even if version validation fails
- `--dry-run`: Show what would be changed without making changes

**Examples:**
```bash
./scripts/update-version.sh 1.2.3
./scripts/update-version.sh 1.2.3 --dry-run
./scripts/update-version.sh 1.2.3 --force
```

**Features:**
- Semantic version validation
- Version comparison (prevents downgrades)
- Automatic backup creation
- Updates all relevant files:
  - Main plugin file (header and constant)
  - README.md (version badges)
  - composer.json (version field)
  - CHANGELOG.md (adds new entry)
- Interactive confirmation
- Comprehensive error handling

---

### ⚡ `update-version-simple.sh` (Simple)
Quick version update without validation or backup.

**Usage:**
```bash
./scripts/update-version-simple.sh <new_version>
```

**Examples:**
```bash
./scripts/update-version-simple.sh 1.2.3
./scripts/update-version-simple.sh 2.0.0
```

**Features:**
- Fast execution
- No validation or backup
- Updates same files as complete version
- Ideal for automated workflows

---

## Script Dependencies

### Required Tools
- `bash` (version 4.0+)
- `grep`, `sed`, `find` (standard Unix tools)
- `git` (for version checking and tagging)
- `zip` (for package creation)
- `bc` (for calculations in size script)

### macOS Compatibility
All scripts are compatible with macOS and use portable commands where possible. The `stat` command uses both BSD and GNU syntax for cross-platform compatibility.

## Integration with GitHub Actions

These scripts are designed to work with the GitHub Actions workflows:

### `calculate-size.sh` → `check-size.yml`
The size calculation script outputs GitHub Actions environment variables for use in the size check workflow.

# Scripts Documentation

This directory contains utility scripts for managing the NextJS GraphQL Hooks plugin development and release process.

## Available Scripts

### `create-release-zip.sh`
**Purpose**: Creates a properly structured ZIP file for WordPress plugin distribution.

**Features**:
- ✅ **Correct Folder Structure**: ZIP filename includes version but internal folder is always `nextjs-graphql-hooks`
- ✅ **WordPress Compatible**: Follows WordPress plugin directory naming conventions
- ✅ **Clean Package**: Excludes development files (.git, node_modules, etc.)
- ✅ **Version Detection**: Automatically extracts version from main plugin file
- ✅ **Size Reporting**: Shows final ZIP size and structure

**Usage**:
```bash
./scripts/create-release-zip.sh
```

**Output**:
- Creates `nextjs-graphql-hooks-v{VERSION}.zip` in project root
- Internal structure: `nextjs-graphql-hooks/` (without version in folder name)
- When extracted in WordPress, creates correct plugin folder name

**Example**:
```bash
# Creates: nextjs-graphql-hooks-v1.0.0.zip
# Contains: nextjs-graphql-hooks/ (folder structure WordPress expects)
```

### `check-versions.sh` → `quality-checks.yml`
Version validation can be integrated into quality checks workflow.

### `update-version.sh` → `release.yml`
The release workflow uses similar logic for version updates during automated releases.

## Best Practices

### Version Management
1. Always use semantic versioning (MAJOR.MINOR.PATCH)
2. Update CHANGELOG.md with meaningful release notes
3. Test version updates with `--dry-run` first
4. Verify changes with `./scripts/check-versions.sh`

### Release Process
1. Update version: `./scripts/update-version.sh X.Y.Z`
2. Edit CHANGELOG.md with proper release notes
3. Verify: `./scripts/check-versions.sh`
4. Check size: `./scripts/calculate-size.sh`
5. Commit and tag: 
   ```bash
   git add .
   git commit -m "Bump version to X.Y.Z"
   git tag vX.Y.Z
   git push && git push --tags
   ```

### Troubleshooting

**Permission Issues:**
```bash
chmod +x scripts/*.sh
```

**Missing Dependencies:**
- Install `bc`: `brew install bc` (macOS)
- Install `git`: Included with Xcode Command Line Tools

**Version Script Fails:**
- Check file encoding (should be UTF-8)
- Verify file paths are correct
- Use `--force` flag to bypass validation

## File Structure

```
scripts/
├── README.md                    # This file
├── calculate-size.sh            # Package size calculator
├── check-versions.sh           # Version consistency checker
├── update-version.sh           # Complete version updater
└── update-version-simple.sh    # Simple version updater
```

## Contributing

When modifying scripts:
1. Test on both macOS and Linux if possible
2. Use portable bash syntax (avoid bashisms)
3. Include error handling with proper exit codes
4. Update this README with any new features
5. Test integration with GitHub Actions workflows
