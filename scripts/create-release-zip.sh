#!/bin/bash

# Create Release ZIP Script
# Creates a properly structured ZIP file for WordPress plugin distribution
# The ZIP will have a versioned filename but the internal folder will be just "nextjs-graphql-hooks"

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== NextJS GraphQL Hooks Release ZIP Creator ===${NC}"
echo ""

# Get version from main plugin file
VERSION=$(grep "Version:" nextjs-graphql-hooks.php | grep -o '[0-9]\+\.[0-9]\+\.[0-9]\+' | head -1)

if [ -z "$VERSION" ]; then
    echo -e "${RED}❌ Error: Could not extract version from nextjs-graphql-hooks.php${NC}"
    exit 1
fi

echo -e "${GREEN}📦 Creating ZIP for version: ${VERSION}${NC}"
echo ""

# Check if vendor directory exists (should have Composer dependencies)
if [ ! -d "vendor" ]; then
    echo -e "${YELLOW}⚠️  Warning: vendor/ directory not found. Run 'composer install' first.${NC}"
fi

# Define files and directories to include
ZIP_NAME="nextjs-graphql-hooks-v${VERSION}.zip"
TEMP_DIR="/tmp/nextjs-graphql-hooks-release"
PLUGIN_DIR="${TEMP_DIR}/nextjs-graphql-hooks"

# Clean up any existing temp directory
if [ -d "$TEMP_DIR" ]; then
    rm -rf "$TEMP_DIR"
fi

# Create temporary directory structure
mkdir -p "$PLUGIN_DIR"

# Install production dependencies
echo -e "${YELLOW}📦 Installing production dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Failed to install production dependencies${NC}"
    exit 1
fi
echo -e "${GREEN}  ✅ Production dependencies installed successfully${NC}"

echo -e "${YELLOW}📋 Copying files...${NC}"

# Copy main plugin files
cp nextjs-graphql-hooks.php "$PLUGIN_DIR/"
cp README.md "$PLUGIN_DIR/"
cp CHANGELOG.md "$PLUGIN_DIR/"
cp LICENSE "$PLUGIN_DIR/"

# Copy includes directory
if [ -d "includes" ]; then
    cp -r includes "$PLUGIN_DIR/"
    echo "  ✅ includes/ directory copied"
fi

# Copy assets directory
if [ -d "assets" ]; then
    cp -r assets "$PLUGIN_DIR/"
    echo "  ✅ assets/ directory copied"
fi

# Copy languages directory if it exists
if [ -d "languages" ]; then
    cp -r languages "$PLUGIN_DIR/"
    echo "  ✅ languages/ directory copied"
fi

# Copy composer.json if it exists (without version field for Packagist compatibility)
if [ -f "composer.json" ]; then
    cp composer.json "$PLUGIN_DIR/"
    echo "  ✅ composer.json copied (version field excluded for Packagist compatibility)"
fi

# Copy optimized vendor dependencies (production only, no tests/dev files)
if [ -d "vendor" ]; then
    echo -e "${YELLOW}📦 Copying optimized vendor dependencies...${NC}"
    
    # Create vendor directory in plugin temp folder
    mkdir -p "$PLUGIN_DIR/vendor"
    
    # Copy Composer autoloader and essential files
    if [ ! -f "vendor/autoload.php" ]; then
        echo -e "${RED}❌ Error: vendor/autoload.php not found. Run 'composer install' first.${NC}"
        exit 1
    fi
    cp vendor/autoload.php "$PLUGIN_DIR/vendor/"
    
    # Copy composer directory but exclude development files
    mkdir -p "$PLUGIN_DIR/vendor/composer"
    
    # Check for required Composer PHP files
    composer_php_files=(vendor/composer/*.php)
    if [ ! -e "${composer_php_files[0]}" ]; then
        echo -e "${RED}❌ Error: No Composer PHP files found in vendor/composer. Run 'composer install' before creating the release ZIP.${NC}"
        exit 1
    fi
    cp "${composer_php_files[@]}" "$PLUGIN_DIR/vendor/composer/"
    
    # Copy optional metadata files
    cp vendor/composer/LICENSE "$PLUGIN_DIR/vendor/composer/" 2>/dev/null || true
    cp vendor/composer/*.json "$PLUGIN_DIR/vendor/composer/" 2>/dev/null || true
    
    # Copy composer/installers (needed for WordPress plugin installation)
    # But exclude tests and dev files
    if [ -d "vendor/composer/installers" ]; then
        mkdir -p "$PLUGIN_DIR/vendor/composer/installers/src"
        
        # Copy optional metadata files
        cp vendor/composer/installers/composer.json "$PLUGIN_DIR/vendor/composer/installers/" 2>/dev/null || true
        cp vendor/composer/installers/LICENSE "$PLUGIN_DIR/vendor/composer/installers/" 2>/dev/null || true
        
        # Copy required src directory (fail if copy fails)
        if [ ! -d "vendor/composer/installers/src" ] || [ -z "$(ls -A vendor/composer/installers/src 2>/dev/null)" ]; then
            echo -e "${RED}❌ Error: composer/installers src/ directory is missing or empty${NC}"
            exit 1
        fi
        if ! cp -r vendor/composer/installers/src/ "$PLUGIN_DIR/vendor/composer/installers/"; then
            echo -e "${RED}❌ Error: Failed to copy composer/installers src/ directory${NC}"
            exit 1
        fi
        
        echo "    ✅ composer/installers (production only)"
    fi
    
    # Copy silverassist packages (production only, no tests)
    if [ -d "vendor/silverassist" ]; then
        mkdir -p "$PLUGIN_DIR/vendor/silverassist"
        
        # Copy wp-github-updater
        if [ -d "vendor/silverassist/wp-github-updater" ]; then
            mkdir -p "$PLUGIN_DIR/vendor/silverassist/wp-github-updater/src"
            mkdir -p "$PLUGIN_DIR/vendor/silverassist/wp-github-updater/assets"
            
            # Copy required files (fail if missing)
            if [ ! -d "vendor/silverassist/wp-github-updater/src" ] || [ -z "$(ls -A vendor/silverassist/wp-github-updater/src 2>/dev/null)" ]; then
                echo -e "${RED}❌ Error: wp-github-updater src/ directory is missing or empty${NC}"
                exit 1
            fi
            if ! cp -r vendor/silverassist/wp-github-updater/src/ "$PLUGIN_DIR/vendor/silverassist/wp-github-updater/"; then
                echo -e "${RED}❌ Error: Failed to copy wp-github-updater src/ directory${NC}"
                exit 1
            fi
            
            # Copy optional files (don't fail if missing)
            cp vendor/silverassist/wp-github-updater/composer.json "$PLUGIN_DIR/vendor/silverassist/wp-github-updater/" 2>/dev/null || true
            cp vendor/silverassist/wp-github-updater/LICENSE.md "$PLUGIN_DIR/vendor/silverassist/wp-github-updater/" 2>/dev/null || true
            cp -r vendor/silverassist/wp-github-updater/assets/ "$PLUGIN_DIR/vendor/silverassist/wp-github-updater/" 2>/dev/null || true
            
            echo "    ✅ silverassist/wp-github-updater (production only)"
        fi
        
        # Copy wp-settings-hub
        if [ -d "vendor/silverassist/wp-settings-hub" ]; then
            mkdir -p "$PLUGIN_DIR/vendor/silverassist/wp-settings-hub/src"
            mkdir -p "$PLUGIN_DIR/vendor/silverassist/wp-settings-hub/assets"
            
            # Copy required files (fail if missing)
            if [ ! -d "vendor/silverassist/wp-settings-hub/src" ] || [ -z "$(ls -A vendor/silverassist/wp-settings-hub/src 2>/dev/null)" ]; then
                echo -e "${RED}❌ Error: wp-settings-hub src/ directory is missing or empty${NC}"
                exit 1
            fi
            if ! cp -r vendor/silverassist/wp-settings-hub/src/ "$PLUGIN_DIR/vendor/silverassist/wp-settings-hub/"; then
                echo -e "${RED}❌ Error: Failed to copy wp-settings-hub src/ directory${NC}"
                exit 1
            fi
            
            # Copy optional files (don't fail if missing)
            cp vendor/silverassist/wp-settings-hub/composer.json "$PLUGIN_DIR/vendor/silverassist/wp-settings-hub/" 2>/dev/null || true
            cp vendor/silverassist/wp-settings-hub/LICENSE "$PLUGIN_DIR/vendor/silverassist/wp-settings-hub/" 2>/dev/null || true
            cp -r vendor/silverassist/wp-settings-hub/assets/ "$PLUGIN_DIR/vendor/silverassist/wp-settings-hub/" 2>/dev/null || true
            
            echo "    ✅ silverassist/wp-settings-hub (production only)"
        fi
    fi
    
    echo "  ✅ Vendor dependencies copied (production files only, tests excluded)"
fi

echo "  ✅ Main plugin files copied"

# Create the ZIP file
echo -e "${YELLOW}🗜️  Creating ZIP archive...${NC}"
cd "$TEMP_DIR"
zip -r "$ZIP_NAME" nextjs-graphql-hooks/ -x "*.DS_Store*" "*.git*" "*node_modules*" "*.log*"

# Move ZIP to project root
mv "$ZIP_NAME" "${OLDPWD}/"
cd "$OLDPWD"

# Clean up temp directory
rm -rf "$TEMP_DIR"

# Restore development dependencies for local environment
echo -e "${YELLOW}📦 Restoring development dependencies for local environment...${NC}"
composer install --no-interaction > /dev/null 2>&1
echo -e "${GREEN}  ✅ Development environment restored${NC}"

# Get ZIP size
ZIP_SIZE=$(du -h "$ZIP_NAME" | cut -f1)

echo ""
echo -e "${GREEN}✅ Release ZIP created successfully!${NC}"
echo -e "${BLUE}📦 File: ${ZIP_NAME}${NC}"
echo -e "${BLUE}📏 Size: ${ZIP_SIZE}${NC}"
echo ""
echo -e "${YELLOW}📂 Internal structure:${NC}"
echo "   nextjs-graphql-hooks/"
echo "   ├── nextjs-graphql-hooks.php"
echo "   ├── README.md"
echo "   ├── CHANGELOG.md"
echo "   ├── LICENSE"
echo "   ├── includes/"
echo "   ├── assets/"
echo "   │   ├── css/"
echo "   │   └── js/"
echo "   ├── composer.json"
echo "   └── vendor/"
echo "       ├── autoload.php"
echo "       ├── composer/"
echo "       │   ├── autoload files"
echo "       │   └── installers/ (production only)"
echo "       └── silverassist/"
echo "           ├── wp-github-updater/ (production only)"
echo "           └── wp-settings-hub/ (production only)"
echo ""
echo -e "${GREEN}🎉 Ready for WordPress installation!${NC}"
echo ""
echo -e "${BLUE}To test the ZIP:${NC}"
echo "1. Upload ${ZIP_NAME} to WordPress admin"
echo "2. The plugin folder will be extracted as 'nextjs-graphql-hooks'"
echo "3. WordPress will recognize it as a valid plugin"
