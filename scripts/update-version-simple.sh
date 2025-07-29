#!/bin/bash

# Update Version Script (Simple)
# Quick version update without validation or backup
# Usage: ./update-version-simple.sh <new_version>

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get new version from argument
NEW_VERSION="$1"

# Function to show usage
show_usage() {
    echo "Usage: $0 <new_version>"
    echo ""
    echo "Examples:"
    echo "  $0 1.2.3"
    echo "  $0 2.0.0"
    echo ""
    echo "Note: This is a simple script without validation or backup."
    echo "For advanced features, use ./update-version.sh instead."
}

# Validate arguments
if [ -z "$NEW_VERSION" ]; then
    echo -e "${RED}Error: Version argument required${NC}"
    echo ""
    show_usage
    exit 1
fi

echo -e "${BLUE}=== Simple Version Update ===${NC}"
echo "Updating to version: $NEW_VERSION"
echo ""

# Update main plugin file
echo -e "${YELLOW}Updating nextjs-graphql-hooks.php...${NC}"
if [ -f "nextjs-graphql-hooks.php" ]; then
    # Update version in header
    sed -i.bak "s/^\(\s*\*\s*Version:\s*\)[0-9]\+\.[0-9]\+\.[0-9]\+/\1$NEW_VERSION/" "nextjs-graphql-hooks.php"
    
    # Update version constant
    sed -i.bak "s/define( 'NEXTJS_GRAPHQL_HOOKS_VERSION', '[0-9]\+\.[0-9]\+\.[0-9]\+' );/define( 'NEXTJS_GRAPHQL_HOOKS_VERSION', '$NEW_VERSION' );/" "nextjs-graphql-hooks.php"
    
    # Remove backup file
    rm -f "nextjs-graphql-hooks.php.bak"
    
    echo -e "  ${GREEN}✅ Updated main plugin file${NC}"
else
    echo -e "  ${RED}❌ Main plugin file not found${NC}"
    exit 1
fi

# Update composer.json
echo -e "${YELLOW}Updating composer.json...${NC}"
if [ -f "composer.json" ]; then
    sed -i.bak "s/\"version\":\s*\"[0-9]\+\.[0-9]\+\.[0-9]\+\"/\"version\": \"$NEW_VERSION\"/" "composer.json"
    rm -f "composer.json.bak"
    echo -e "  ${GREEN}✅ Updated composer.json${NC}"
else
    echo -e "  ${YELLOW}⚠️  composer.json not found, skipping${NC}"
fi

# Update README.md version badges/references
echo -e "${YELLOW}Updating README.md...${NC}"
if [ -f "README.md" ]; then
    sed -i.bak "s/version-[0-9]\+\.[0-9]\+\.[0-9]\+/version-$NEW_VERSION/g" "README.md"
    sed -i.bak "s/v[0-9]\+\.[0-9]\+\.[0-9]\+/v$NEW_VERSION/g" "README.md"
    rm -f "README.md.bak"
    echo -e "  ${GREEN}✅ Updated README.md${NC}"
else
    echo -e "  ${YELLOW}⚠️  README.md not found, skipping${NC}"
fi

# Add entry to CHANGELOG.md
echo -e "${YELLOW}Updating CHANGELOG.md...${NC}"
if [ -f "CHANGELOG.md" ]; then
    # Create temporary file
    temp_file=$(mktemp)
    date_today=$(date +%Y-%m-%d)
    
    # Add new version entry
    {
        head -n 1 "CHANGELOG.md"  # Keep the main title
        echo ""
        echo "## [$NEW_VERSION] - $date_today"
        echo ""
        echo "### Added"
        echo "- Version $NEW_VERSION release"
        echo ""
        tail -n +2 "CHANGELOG.md"  # Rest of the file
    } > "$temp_file"
    
    mv "$temp_file" "CHANGELOG.md"
    echo -e "  ${GREEN}✅ Updated CHANGELOG.md${NC}"
else
    echo -e "  ${YELLOW}⚠️  CHANGELOG.md not found, skipping${NC}"
fi

echo ""
echo -e "${GREEN}✅ Version update completed!${NC}"
echo ""
echo "📋 Files updated:"
echo "  • nextjs-graphql-hooks.php"
echo "  • composer.json"
echo "  • README.md"
echo "  • CHANGELOG.md"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "  1. Review changes: git diff"
echo "  2. Commit: git add . && git commit -m \"Bump version to $NEW_VERSION\""
echo "  3. Tag: git tag v$NEW_VERSION"
echo "  4. Push: git push && git push --tags"
