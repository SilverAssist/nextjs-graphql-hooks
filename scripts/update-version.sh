#!/bin/bash

###############################################################################
# NextJS GraphQL Hooks Plugin - Version Update Script
#
# Automatically updates version numbers across all plugin files including:
# - Main plugin file constant and header
# - All PHP files @version tags
# - All CSS files @version tags  
# - All JavaScript files @version tags
# - Block metadata (block.json)
#
# Usage: ./scripts/update-version.sh <new-version>
# Example: ./scripts/update-version.sh 1.0.2
#
# @package NextJSGraphQLHooks
# @since 1.0.0
# @author Silver Assist
# @version 1.0.0
###############################################################################

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to show usage
show_usage() {
    echo "Usage: $0 <new-version>"
    echo ""
    echo "Examples:"
    echo "  $0 1.0.2"
    echo "  $0 1.1.0"
    echo "  $0 2.0.0"
    echo ""
    echo "This script will update version numbers in:"
    echo "  - Main plugin file (nextjs-graphql-hooks.php)"
    echo "  - All PHP files (@version tags)"
}

# Validate input
if [ $# -eq 0 ]; then
    print_error "No version specified"
    show_usage
    exit 1
fi

NEW_VERSION="$1"

# Validate version format (basic semantic versioning)
if ! [[ $NEW_VERSION =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    print_error "Invalid version format. Use semantic versioning (e.g., 1.0.2)"
    exit 1
fi

# Get current directory (should be project root)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

print_status "Updating NextJS GraphQL Hooks Plugin to version ${NEW_VERSION}"
print_status "Project root: ${PROJECT_ROOT}"

# Check if we're in the right directory
if [ ! -f "${PROJECT_ROOT}/nextjs-graphql-hooks.php" ]; then
    print_error "Main plugin file not found. Make sure you're running this from the project root."
    exit 1
fi

# Get current version from main plugin file
CURRENT_VERSION=$(grep -o "Version: [0-9]\+\.[0-9]\+\.[0-9]\+" "${PROJECT_ROOT}/nextjs-graphql-hooks.php" | cut -d' ' -f2)

if [ -z "$CURRENT_VERSION" ]; then
    print_error "Could not detect current version from main plugin file"
    exit 1
fi

print_status "Current version: ${CURRENT_VERSION}"
print_status "New version: ${NEW_VERSION}"

# Confirm with user
echo ""
read -p "$(echo -e ${YELLOW}[CONFIRM]${NC} Update version from ${CURRENT_VERSION} to ${NEW_VERSION}? [y/N]: )" -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_warning "Version update cancelled"
    exit 0
fi

echo ""
print_status "Starting version update process..."

# Function to update version in file with backup
update_version_in_file() {
    local file="$1"
    local pattern="$2"
    local replacement="$3"
    local description="$4"
    
    if [ -f "$file" ]; then
        # Create backup
        cp "$file" "$file.bak"
        
        # Perform replacement
        if sed -i '' "$pattern" "$file" 2>/dev/null; then
            # Verify the change was made
            if ! cmp -s "$file" "$file.bak"; then
                print_status "  Updated $description"
                rm "$file.bak"
                return 0
            else
                print_warning "  No changes made to $description (pattern not found)"
                mv "$file.bak" "$file"
                return 1
            fi
        else
            print_error "  Failed to update $description"
            mv "$file.bak" "$file"
            return 1
        fi
    else
        print_warning "  File not found: $file"
        return 1
    fi
}

# 1. Update main plugin file
print_status "Updating main plugin file..."

# Update plugin header version
update_version_in_file "${PROJECT_ROOT}/nextjs-graphql-hooks.php" \
    "s/Version: [0-9]\+\.[0-9]\+\.[0-9]\+/Version: ${NEW_VERSION}/g" \
    "${NEW_VERSION}" \
    "plugin header"

# Update constant
update_version_in_file "${PROJECT_ROOT}/nextjs-graphql-hooks.php" \
    "s/define(\"NEXTJS_GRAPHQL_HOOKS_VERSION\", \"[0-9]\+\.[0-9]\+\.[0-9]\+\")/define(\"NEXTJS_GRAPHQL_HOOKS_VERSION\", \"${NEW_VERSION}\")/g" \
    "${NEW_VERSION}" \
    "plugin constant"

# Update @version tag in main file
update_version_in_file "${PROJECT_ROOT}/nextjs-graphql-hooks.php" \
    "s/@version [0-9]\+\.[0-9]\+\.[0-9]\+/@version ${NEW_VERSION}/g" \
    "${NEW_VERSION}" \
    "main file @version tag"

print_success "Main plugin file updated"

# 2. Update all PHP files in includes/
print_status "Updating PHP files..."

updated_count=0
find "${PROJECT_ROOT}/includes" -name "*.php" -type f | while read -r file; do
    if grep -q "@version" "$file"; then
        if update_version_in_file "$file" \
            "s/@version [0-9]\+\.[0-9]\+\.[0-9]\+/@version ${NEW_VERSION}/g" \
            "${NEW_VERSION}" \
            "$(basename "$file")"; then
            ((updated_count++))
        fi
    else
        print_warning "  No @version tag found in $(basename "$file")"
    fi
done

print_success "PHP files updated ($updated_count files)"


# 3. Update this script's version
print_status "Updating version update script..."
update_version_in_file "${PROJECT_ROOT}/scripts/update-version.sh" \
    "s/@version [0-9]\+\.[0-9]\+\.[0-9]\+/@version ${NEW_VERSION}/g" \
    "${NEW_VERSION}" \
    "update script"

print_success "Version update script updated"

echo ""
print_success "✨ Version update completed successfully!"
echo ""
print_status "Summary of changes:"
echo "  • Main plugin file: nextjs-graphql-hooks.php"
echo "  • PHP files: includes/**/*.php"
echo "  • Update script: scripts/update-version.sh"
echo ""
print_status "Next steps:"
echo "  1. Review the changes: git diff"
echo "  2. Test the plugin with new version"
echo "  3. Update CHANGELOG.md manually (if needed)"
echo "  4. Commit changes: git add . && git commit -m '🔧 Update version to ${NEW_VERSION}'"
echo "  5. Create tag: git tag v${NEW_VERSION}"
echo "  6. Push changes: git push origin main && git push origin v${NEW_VERSION}"
echo ""
print_warning "Remember: This script only updates @version tags, not @since tags!"
print_warning "New files should have their @since tag set manually to the version when they were introduced."
