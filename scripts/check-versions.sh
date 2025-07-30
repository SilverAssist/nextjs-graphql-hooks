#!/bin/bash

###############################################################################
# NextJS GraphQL Hooks Plugin - Version Check Script
#
# Checks and displays current version numbers across all plugin files
# Useful for verifying version consistency before and after updates
#
# Usage: ./scripts/check-versions.sh
#
# @package NextJSGraphQLHooks
# @since 1.0.0
# @author Silver Assist
# @version 1.0.0
###############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Function to print colored output
print_header() {
    echo -e "${CYAN}=== $1 ===${NC}"
}

print_file() {
    echo -e "${BLUE}📄 $1${NC}"
}

print_version() {
    echo -e "   ${GREEN}Version: $1${NC}"
}

print_error() {
    echo -e "   ${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "   ${YELLOW}⚠️  $1${NC}"
}

# Get current directory (should be project root)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo -e "${CYAN}"
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                    VERSION CHECK REPORT                     ║"
echo "║                 NextJS GraphQL Hooks Plugin                 ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Check if we're in the right directory
if [ ! -f "${PROJECT_ROOT}/nextjs-graphql-hooks.php" ]; then
    print_error "Main plugin file not found. Make sure you're running this from the project root."
    exit 1
fi

print_header "Main Plugin File"
print_file "nextjs-graphql-hooks.php"

# Extract versions from main file
PLUGIN_HEADER_VERSION=$(grep -o "Version: [0-9]\+\.[0-9]\+\.[0-9]\+" "${PROJECT_ROOT}/nextjs-graphql-hooks.php" | cut -d' ' -f2)
PLUGIN_CONSTANT_VERSION=$(grep -o "NEXTJS_GRAPHQL_HOOKS_VERSION.*[0-9]\+\.[0-9]\+\.[0-9]\+" "${PROJECT_ROOT}/nextjs-graphql-hooks.php" | grep -o "[0-9]\+\.[0-9]\+\.[0-9]\+")
PLUGIN_DOCBLOCK_VERSION=$(grep -o "@version [0-9]\+\.[0-9]\+\.[0-9]\+" "${PROJECT_ROOT}/nextjs-graphql-hooks.php" | cut -d' ' -f2)

if [ -n "$PLUGIN_HEADER_VERSION" ]; then
    print_version "Plugin Header: $PLUGIN_HEADER_VERSION"
else
    print_error "Plugin header version not found"
fi

if [ -n "$PLUGIN_CONSTANT_VERSION" ]; then
    print_version "Plugin Constant: $PLUGIN_CONSTANT_VERSION"
else
    print_error "Plugin constant version not found"
fi

if [ -n "$PLUGIN_DOCBLOCK_VERSION" ]; then
    print_version "DocBlock: $PLUGIN_DOCBLOCK_VERSION"
else
    print_error "DocBlock version not found"
fi

# Set main version for comparison
MAIN_VERSION="$PLUGIN_HEADER_VERSION"

echo ""
print_header "PHP Files (includes/)"

find "${PROJECT_ROOT}/includes" -name "*.php" -type f | sort | while read -r file; do
    filename=$(basename "$file")
    print_file "$filename"
    
    version=$(grep -o "@version [0-9]\+\.[0-9]\+\.[0-9]\+" "$file" 2>/dev/null | cut -d' ' -f2)
    
    if [ -n "$version" ]; then
        if [ "$version" = "$MAIN_VERSION" ]; then
            print_version "$version ✓"
        else
            print_warning "$version (differs from main: $MAIN_VERSION)"
        fi
    else
        print_error "No @version tag found"
    fi
done

echo ""
print_header "Summary"

if [ -n "$MAIN_VERSION" ]; then
    echo -e "${GREEN}✓ Main plugin version: $MAIN_VERSION${NC}"
else
    echo -e "${RED}❌ Could not determine main plugin version${NC}"
fi

echo ""
echo -e "${BLUE}💡 Tips:${NC}"
echo "• Use ${YELLOW}./scripts/update-version.sh <version>${NC} to update all versions"
echo "• Green checkmarks (✓) indicate files matching the main version"
echo "• Warnings (⚠️) indicate version mismatches that may need attention"
echo "• Errors (❌) indicate missing version tags"
