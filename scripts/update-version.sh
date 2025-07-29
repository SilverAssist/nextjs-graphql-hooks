#!/bin/bash

# Update Version Script (Complete)
# Updates version across all plugin files with validation and backup
# Usage: ./update-version.sh <new_version> [--force] [--dry-run]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PLUGIN_SLUG="nextjs-graphql-hooks"
BACKUP_DIR="./backups/version-update-$(date +%Y%m%d-%H%M%S)"

# Parse arguments
NEW_VERSION=""
FORCE_UPDATE=false
DRY_RUN=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --force)
            FORCE_UPDATE=true
            shift
            ;;
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        *)
            if [ -z "$NEW_VERSION" ]; then
                NEW_VERSION="$1"
            else
                echo -e "${RED}Error: Multiple version arguments provided${NC}"
                exit 1
            fi
            shift
            ;;
    esac
done

# Function to show usage
show_usage() {
    echo "Usage: $0 <new_version> [options]"
    echo ""
    echo "Options:"
    echo "  --force      Force update even if version validation fails"
    echo "  --dry-run    Show what would be changed without making changes"
    echo ""
    echo "Examples:"
    echo "  $0 1.2.3"
    echo "  $0 1.2.3 --dry-run"
    echo "  $0 1.2.3 --force"
}

# Validate arguments
if [ -z "$NEW_VERSION" ]; then
    echo -e "${RED}Error: Version argument required${NC}"
    echo ""
    show_usage
    exit 1
fi

# Function to validate semantic version
is_valid_semver() {
    if [[ $1 =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
        return 0
    else
        return 1
    fi
}

# Function to get current version from main file
get_current_version() {
    if [ -f "nextjs-graphql-hooks.php" ]; then
        grep -E "^\s*\*\s*Version:" "nextjs-graphql-hooks.php" | head -1 | sed -E 's/.*Version:\s*([0-9]+\.[0-9]+\.[0-9]+).*/\1/'
    else
        echo "NOT_FOUND"
    fi
}

# Function to compare versions
version_compare() {
    if [[ $1 == $2 ]]; then
        echo "equal"
    else
        if [[ $1 == $(echo -e "$1\n$2" | sort -V | head -1) ]]; then
            echo "less"
        else
            echo "greater"
        fi
    fi
}

# Function to create backup
create_backup() {
    if [ "$DRY_RUN" = false ]; then
        echo -e "${YELLOW}Creating backup...${NC}"
        mkdir -p "$BACKUP_DIR"
        
        # Backup files that will be modified
        local files_to_backup=(
            "nextjs-graphql-hooks.php"
            "README.md"
            "composer.json"
            "CHANGELOG.md"
        )
        
        for file in "${files_to_backup[@]}"; do
            if [ -f "$file" ]; then
                cp "$file" "$BACKUP_DIR/"
                echo "  📄 Backed up: $file"
            fi
        done
        
        echo -e "${GREEN}✅ Backup created at: $BACKUP_DIR${NC}"
    fi
}

# Function to update main plugin file
update_main_plugin() {
    local file="nextjs-graphql-hooks.php"
    if [ -f "$file" ]; then
        if [ "$DRY_RUN" = true ]; then
            echo "  📄 Would update: $file"
            echo "    Version header: * Version: $NEW_VERSION"
            echo "    Constant: NEXTJS_GRAPHQL_HOOKS_VERSION = '$NEW_VERSION'"
        else
            # Update version in header
            sed -i.bak "s/^\(\s*\*\s*Version:\s*\)[0-9]\+\.[0-9]\+\.[0-9]\+/\1$NEW_VERSION/" "$file"
            
            # Update version constant
            sed -i.bak "s/define( 'NEXTJS_GRAPHQL_HOOKS_VERSION', '[0-9]\+\.[0-9]\+\.[0-9]\+' );/define( 'NEXTJS_GRAPHQL_HOOKS_VERSION', '$NEW_VERSION' );/" "$file"
            
            # Remove backup file
            rm -f "${file}.bak"
            
            echo -e "  ${GREEN}✅ Updated: $file${NC}"
        fi
    else
        echo -e "  ${RED}❌ File not found: $file${NC}"
        return 1
    fi
}

# Function to update README.md
update_readme() {
    local file="README.md"
    if [ -f "$file" ]; then
        if [ "$DRY_RUN" = true ]; then
            echo "  📄 Would update: $file"
            echo "    Version references"
        else
            # Update version badges or references
            sed -i.bak "s/version-[0-9]\+\.[0-9]\+\.[0-9]\+/version-$NEW_VERSION/g" "$file"
            sed -i.bak "s/v[0-9]\+\.[0-9]\+\.[0-9]\+/v$NEW_VERSION/g" "$file"
            
            # Remove backup file
            rm -f "${file}.bak"
            
            echo -e "  ${GREEN}✅ Updated: $file${NC}"
        fi
    else
        echo -e "  ${YELLOW}⚠️  File not found: $file${NC}"
    fi
}

# Function to update composer.json
update_composer() {
    local file="composer.json"
    if [ -f "$file" ]; then
        if [ "$DRY_RUN" = true ]; then
            echo "  📄 Would update: $file"
            echo "    \"version\": \"$NEW_VERSION\""
        else
            # Update version field
            sed -i.bak "s/\"version\":\s*\"[0-9]\+\.[0-9]\+\.[0-9]\+\"/\"version\": \"$NEW_VERSION\"/" "$file"
            
            # Remove backup file
            rm -f "${file}.bak"
            
            echo -e "  ${GREEN}✅ Updated: $file${NC}"
        fi
    else
        echo -e "  ${YELLOW}⚠️  File not found: $file${NC}"
    fi
}

# Function to update CHANGELOG.md
update_changelog() {
    local file="CHANGELOG.md"
    if [ -f "$file" ]; then
        if [ "$DRY_RUN" = true ]; then
            echo "  📄 Would update: $file"
            echo "    Add new version entry"
        else
            # Create temporary file with new version entry
            local temp_file=$(mktemp)
            local date_today=$(date +%Y-%m-%d)
            
            # Add new version entry after the main header
            {
                head -n 1 "$file"  # Keep the main title
                echo ""
                echo "## [$NEW_VERSION] - $date_today"
                echo ""
                echo "### Added"
                echo "- Version $NEW_VERSION release"
                echo ""
                tail -n +2 "$file"  # Rest of the file
            } > "$temp_file"
            
            mv "$temp_file" "$file"
            
            echo -e "  ${GREEN}✅ Updated: $file${NC}"
            echo -e "    ${YELLOW}Note: Please edit CHANGELOG.md to add proper release notes${NC}"
        fi
    else
        echo -e "  ${YELLOW}⚠️  File not found: $file${NC}"
    fi
}

# Main execution
echo -e "${BLUE}=== NextJS GraphQL Hooks Version Updater ===${NC}"
echo ""

# Validate new version format
if ! is_valid_semver "$NEW_VERSION"; then
    echo -e "${RED}Error: Invalid semantic version format: $NEW_VERSION${NC}"
    echo -e "${RED}Expected format: MAJOR.MINOR.PATCH (e.g., 1.2.3)${NC}"
    exit 1
fi

# Get current version
CURRENT_VERSION=$(get_current_version)

if [ "$CURRENT_VERSION" = "NOT_FOUND" ]; then
    echo -e "${RED}Error: Could not determine current version${NC}"
    exit 1
fi

echo "📋 Version Update Information:"
echo "=============================="
echo "  Current version: $CURRENT_VERSION"
echo "  New version:     $NEW_VERSION"

if [ "$DRY_RUN" = true ]; then
    echo -e "  ${YELLOW}Mode: DRY RUN (no changes will be made)${NC}"
fi

echo ""

# Version comparison
COMPARISON=$(version_compare "$NEW_VERSION" "$CURRENT_VERSION")
case $COMPARISON in
    "equal")
        if [ "$FORCE_UPDATE" = false ]; then
            echo -e "${YELLOW}Warning: New version is the same as current version${NC}"
            echo -e "${YELLOW}Use --force to proceed anyway${NC}"
            exit 1
        else
            echo -e "${YELLOW}Warning: Forcing update to same version${NC}"
        fi
        ;;
    "less")
        if [ "$FORCE_UPDATE" = false ]; then
            echo -e "${RED}Error: New version is older than current version${NC}"
            echo -e "${RED}Use --force to proceed anyway${NC}"
            exit 1
        else
            echo -e "${YELLOW}Warning: Forcing downgrade to older version${NC}"
        fi
        ;;
    "greater")
        echo -e "${GREEN}✅ Version update is valid${NC}"
        ;;
esac

echo ""

# Confirm update
if [ "$DRY_RUN" = false ] && [ "$FORCE_UPDATE" = false ]; then
    read -p "Proceed with version update? (y/N): " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Update cancelled."
        exit 0
    fi
fi

# Create backup
create_backup

# Update files
echo -e "${YELLOW}Updating version in files...${NC}"

update_main_plugin
update_readme
update_composer
update_changelog

echo ""

if [ "$DRY_RUN" = false ]; then
    echo -e "${GREEN}✅ Version update completed successfully!${NC}"
    echo ""
    echo "📋 Next Steps:"
    echo "=============="
    echo "1. Review changes with: git diff"
    echo "2. Edit CHANGELOG.md to add proper release notes"
    echo "3. Test the plugin functionality"
    echo "4. Commit changes: git add . && git commit -m \"Bump version to $NEW_VERSION\""
    echo "5. Create git tag: git tag v$NEW_VERSION"
    echo "6. Push changes: git push && git push --tags"
    echo ""
    echo -e "${BLUE}Backup created at: $BACKUP_DIR${NC}"
else
    echo -e "${YELLOW}Dry run completed. No files were modified.${NC}"
    echo -e "${YELLOW}Run without --dry-run to apply changes.${NC}"
fi
