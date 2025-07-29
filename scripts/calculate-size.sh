#!/bin/bash

# Calculate Package Size Script
# Calculates the distribution package size for NextJS GraphQL Hooks plugin
# Used by GitHub Actions and manual testing

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PLUGIN_SLUG="nextjs-graphql-hooks"
TMP_DIR="./tmp"
PACKAGE_DIR="${TMP_DIR}/${PLUGIN_SLUG}"
PACKAGE_ZIP="${TMP_DIR}/${PLUGIN_SLUG}.zip"

# Files to include in the package
INCLUDE_FILES=(
    "nextjs-graphql-hooks.php"
    "includes/"
    "README.md"
    "LICENSE"
    "CHANGELOG.md"
    "composer.json"
    "languages/"
)

# Files to exclude
EXCLUDE_PATTERNS=(
    "*.git*"
    "*.github*"
    "*.phpcs.xml*"
    "scripts/"
    "docs/"
    "examples/"
    "tmp/"
    "vendor/"
    "composer.lock"
    "copilot-instructions.md"
    "INSTALLATION.md"
    "node_modules/"
    "*.log"
    "*.tmp"
    ".DS_Store"
    "Thumbs.db"
)

echo -e "${BLUE}=== NextJS GraphQL Hooks Package Size Calculator ===${NC}"
echo ""

# Function to convert bytes to human readable format
convert_bytes() {
    local bytes=$1
    if [ $bytes -ge 1073741824 ]; then
        echo "$(echo "scale=2; $bytes/1073741824" | bc)GB"
    elif [ $bytes -ge 1048576 ]; then
        echo "$(echo "scale=2; $bytes/1048576" | bc)MB"
    elif [ $bytes -ge 1024 ]; then
        echo "$(echo "scale=2; $bytes/1024" | bc)KB"
    else
        echo "${bytes}B"
    fi
}

# Clean previous builds
echo -e "${YELLOW}Cleaning previous builds...${NC}"
rm -rf "$TMP_DIR"
mkdir -p "$PACKAGE_DIR"

# Copy files to package directory
echo -e "${YELLOW}Copying files to package directory...${NC}"
for file in "${INCLUDE_FILES[@]}"; do
    if [ -e "$file" ]; then
        if [ -d "$file" ]; then
            echo "  📁 Copying directory: $file"
            # Ensure the parent directory exists and copy preserving structure
            mkdir -p "$PACKAGE_DIR/$(dirname "$file")"
            cp -r "$file" "$PACKAGE_DIR/$file"
        else
            echo "  📄 Copying file: $file"
            # Ensure the parent directory exists
            mkdir -p "$PACKAGE_DIR/$(dirname "$file")"
            cp "$file" "$PACKAGE_DIR/$file"
        fi
    else
        echo -e "  ${YELLOW}⚠️  Warning: $file not found${NC}"
    fi
done

# Remove excluded files/patterns
echo -e "${YELLOW}Removing excluded files...${NC}"
for pattern in "${EXCLUDE_PATTERNS[@]}"; do
    find "$PACKAGE_DIR" -name "$pattern" -type f -delete 2>/dev/null || true
    find "$PACKAGE_DIR" -name "$pattern" -type d -exec rm -rf {} + 2>/dev/null || true
done

# Calculate individual file sizes
echo -e "${YELLOW}Calculating file sizes...${NC}"
echo ""
echo "📊 File breakdown:"
echo "===================="

total_files=0
total_size=0

while IFS= read -r -d '' file; do
    if [ -f "$file" ]; then
        size=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null || echo "0")
        relative_path=${file#$PACKAGE_DIR/}
        size_human=$(convert_bytes "$size")
        printf "  %-40s %10s\n" "$relative_path" "$size_human"
        total_files=$((total_files + 1))
        total_size=$((total_size + size))
    fi
done < <(find "$PACKAGE_DIR" -type f -print0 | sort -z)

echo "===================="
echo -e "${GREEN}Total files: $total_files${NC}"
echo -e "${GREEN}Total size: $(convert_bytes $total_size)${NC}"
echo ""

# Create ZIP package
echo -e "${YELLOW}Creating ZIP package...${NC}"
cd "$TMP_DIR"
zip -r "${PLUGIN_SLUG}.zip" "$PLUGIN_SLUG/" -x "*.DS_Store" > /dev/null
cd - > /dev/null

# Calculate ZIP size
if [ -f "$PACKAGE_ZIP" ]; then
    zip_size=$(stat -f%z "$PACKAGE_ZIP" 2>/dev/null || stat -c%s "$PACKAGE_ZIP" 2>/dev/null || echo "0")
    zip_size_human=$(convert_bytes "$zip_size")
    
    echo -e "${GREEN}✅ Package created successfully!${NC}"
    echo ""
    echo "📦 Package Information:"
    echo "======================="
    echo "  Package file: $PACKAGE_ZIP"
    echo "  Uncompressed: $(convert_bytes $total_size)"
    echo "  Compressed:   $zip_size_human"
    
    # Calculate compression ratio
    if [ $total_size -gt 0 ]; then
        ratio=$(echo "scale=1; (($total_size - $zip_size) * 100) / $total_size" | bc)
        echo "  Compression:  ${ratio}%"
    fi
    
    echo ""
    
    # Size warnings
    if [ $zip_size -gt 5242880 ]; then # 5MB
        echo -e "${RED}⚠️  Warning: Package size is larger than 5MB${NC}"
        echo -e "${RED}   Consider optimizing or removing unnecessary files${NC}"
    elif [ $zip_size -gt 2097152 ]; then # 2MB
        echo -e "${YELLOW}⚠️  Package size is larger than 2MB${NC}"
        echo -e "${YELLOW}   Consider reviewing included files${NC}"
    else
        echo -e "${GREEN}✅ Package size is within acceptable limits${NC}"
    fi
    
    # Output for GitHub Actions
    if [ "$GITHUB_ACTIONS" = "true" ]; then
        echo "PACKAGE_SIZE=$zip_size" >> $GITHUB_OUTPUT
        echo "PACKAGE_SIZE_HUMAN=$zip_size_human" >> $GITHUB_OUTPUT
        echo "PACKAGE_FILE=$PACKAGE_ZIP" >> $GITHUB_OUTPUT
        echo "UNCOMPRESSED_SIZE=$total_size" >> $GITHUB_OUTPUT
        echo "COMPRESSION_RATIO=$ratio" >> $GITHUB_OUTPUT
    fi
    
    # Return size in bytes for scripts
    echo "$zip_size"
else
    echo -e "${RED}❌ Failed to create package${NC}"
    exit 1
fi
