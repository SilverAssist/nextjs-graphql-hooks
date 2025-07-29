#!/bin/bash

# Check Versions Script
# Verifies version consistency across all plugin files
# Used for quality control and release preparation

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== NextJS GraphQL Hooks Version Checker ===${NC}"
echo ""

# Files to check for versions
declare -A VERSION_FILES=(
    ["Main Plugin"]="nextjs-graphql-hooks.php"
    ["README.md"]="README.md"
    ["Composer"]="composer.json"
    ["CHANGELOG"]="CHANGELOG.md"
)

# Function to extract version from main plugin file
get_main_version() {
    if [ -f "nextjs-graphql-hooks.php" ]; then
        grep "Version:" "nextjs-graphql-hooks.php" | grep -o '[0-9]\+\.[0-9]\+\.[0-9]\+' | head -1
    else
        echo "FILE_NOT_FOUND"
    fi
}

# Function to extract version from README.md
get_readme_version() {
    if [ -f "README.md" ]; then
        # Look for version in plugin header or version badge
        grep -E "(Version|version).*[0-9]+\.[0-9]+\.[0-9]+" "README.md" | grep -o '[0-9]\+\.[0-9]\+\.[0-9]\+' | head -1 || echo "NOT_FOUND"
    else
        echo "FILE_NOT_FOUND"
    fi
}

# Function to extract version from composer.json
get_composer_version() {
    if [ -f "composer.json" ]; then
        grep '"version"' "composer.json" | grep -o '[0-9]\+\.[0-9]\+\.[0-9]\+' | head -1 || echo "NOT_FOUND"
    else
        echo "FILE_NOT_FOUND"
    fi
}

# Function to extract latest version from CHANGELOG.md
get_changelog_version() {
    if [ -f "CHANGELOG.md" ]; then
        # Look for version headers like ## [1.0.0] or ## Version 1.0.0
        grep -E "^##\s*(\[)?[vV]?([0-9]+\.[0-9]+\.[0-9]+)" "CHANGELOG.md" | head -1 | sed -E 's/.*[vV]?([0-9]+\.[0-9]+\.[0-9]+).*/\1/' || echo "NOT_FOUND"
    else
        echo "FILE_NOT_FOUND"
    fi
}

# Function to validate semantic version format
is_valid_semver() {
    if [[ $1 =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
        return 0
    else
        return 1
    fi
}

# Function to compare versions
version_compare() {
    if [[ $1 == $2 ]]; then
        echo "equal"
    else
        # Use sort -V for version comparison
        if [[ $1 == $(echo -e "$1\n$2" | sort -V | head -1) ]]; then
            echo "less"
        else
            echo "greater"
        fi
    fi
}

# Get versions from all files
echo -e "${YELLOW}Extracting versions from files...${NC}"
echo ""

main_version=$(get_main_version)
readme_version=$(get_readme_version)
composer_version=$(get_composer_version)
changelog_version=$(get_changelog_version)

# Display found versions
echo "📋 Version Information:"
echo "======================"
printf "  %-15s %s\n" "Main Plugin:" "$main_version"
printf "  %-15s %s\n" "README.md:" "$readme_version"
printf "  %-15s %s\n" "Composer:" "$composer_version"
printf "  %-15s %s\n" "CHANGELOG:" "$changelog_version"
echo ""

# Check for issues
issues_found=0
reference_version=""

# Determine reference version (from main plugin file)
if [ "$main_version" != "FILE_NOT_FOUND" ] && [ "$main_version" != "NOT_FOUND" ]; then
    reference_version="$main_version"
    echo -e "${GREEN}✅ Using main plugin version as reference: $reference_version${NC}"
else
    echo -e "${RED}❌ Could not extract version from main plugin file${NC}"
    issues_found=$((issues_found + 1))
fi

# Validate semantic versioning
echo ""
echo "🔍 Semantic Version Validation:"
echo "==============================="

for version in "$main_version" "$readme_version" "$composer_version" "$changelog_version"; do
    if [ "$version" != "FILE_NOT_FOUND" ] && [ "$version" != "NOT_FOUND" ]; then
        if is_valid_semver "$version"; then
            echo -e "  ${GREEN}✅ $version - Valid semver${NC}"
        else
            echo -e "  ${RED}❌ $version - Invalid semver format${NC}"
            issues_found=$((issues_found + 1))
        fi
    fi
done

# Check version consistency
if [ -n "$reference_version" ]; then
    echo ""
    echo "🔄 Version Consistency Check:"
    echo "============================="
    
    # Check README version
    if [ "$readme_version" != "FILE_NOT_FOUND" ] && [ "$readme_version" != "NOT_FOUND" ]; then
        if [ "$readme_version" = "$reference_version" ]; then
            echo -e "  ${GREEN}✅ README.md version matches${NC}"
        else
            echo -e "  ${RED}❌ README.md version mismatch: $readme_version (expected: $reference_version)${NC}"
            issues_found=$((issues_found + 1))
        fi
    else
        echo -e "  ${YELLOW}⚠️  README.md version not found or file missing${NC}"
    fi
    
    # Check Composer version
    if [ "$composer_version" != "FILE_NOT_FOUND" ] && [ "$composer_version" != "NOT_FOUND" ]; then
        if [ "$composer_version" = "$reference_version" ]; then
            echo -e "  ${GREEN}✅ Composer.json version matches${NC}"
        else
            echo -e "  ${RED}❌ Composer.json version mismatch: $composer_version (expected: $reference_version)${NC}"
            issues_found=$((issues_found + 1))
        fi
    else
        echo -e "  ${YELLOW}⚠️  Composer.json version not found or file missing${NC}"
    fi
    
    # Check CHANGELOG version
    if [ "$changelog_version" != "FILE_NOT_FOUND" ] && [ "$changelog_version" != "NOT_FOUND" ]; then
        comparison=$(version_compare "$changelog_version" "$reference_version")
        case $comparison in
            "equal")
                echo -e "  ${GREEN}✅ CHANGELOG.md version matches${NC}"
                ;;
            "greater")
                echo -e "  ${YELLOW}⚠️  CHANGELOG.md has newer version: $changelog_version${NC}"
                echo -e "     ${YELLOW}Consider updating main plugin version${NC}"
                ;;
            "less")
                echo -e "  ${RED}❌ CHANGELOG.md has older version: $changelog_version (expected: $reference_version)${NC}"
                issues_found=$((issues_found + 1))
                ;;
        esac
    else
        echo -e "  ${YELLOW}⚠️  CHANGELOG.md version not found or file missing${NC}"
    fi
fi

# Check if Git tag exists for current version
if [ -n "$reference_version" ]; then
    echo ""
    echo "🏷️  Git Tag Check:"
    echo "================="
    
    if git tag -l | grep -q "^v${reference_version}$"; then
        echo -e "  ${GREEN}✅ Git tag v${reference_version} exists${NC}"
    else
        echo -e "  ${YELLOW}⚠️  Git tag v${reference_version} does not exist${NC}"
        echo -e "     ${YELLOW}Run: git tag v${reference_version}${NC}"
    fi
fi

# Final summary
echo ""
echo "📊 Summary:"
echo "==========="

if [ $issues_found -eq 0 ]; then
    echo -e "${GREEN}✅ All version checks passed!${NC}"
    echo -e "${GREEN}   Ready for release${NC}"
    exit 0
else
    echo -e "${RED}❌ Found $issues_found version issues${NC}"
    echo -e "${RED}   Please fix version inconsistencies before release${NC}"
    
    # Suggest fixes
    echo ""
    echo -e "${YELLOW}💡 Suggested fixes:${NC}"
    echo "   1. Run: ./scripts/update-version.sh $reference_version"
    echo "   2. Review and update CHANGELOG.md"
    echo "   3. Re-run this script to verify"
    
    exit 1
fi
