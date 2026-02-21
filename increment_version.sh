#!/bin/bash

# Script to increment APP_VERSION by 0.02 in both .env files

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to increment version
increment_version() {
    local env_file=$1
    
    if [ ! -f "$env_file" ]; then
        echo -e "${RED}Error: File $env_file does not exist${NC}"
        return 1
    fi
    
    # Extract current APP_VERSION value
    current_version=$(grep "^APP_VERSION=" "$env_file" | cut -d'=' -f2 | tr -d ' ' | tr -d '"' | tr -d "'")
    
    if [ -z "$current_version" ]; then
        echo -e "${YELLOW}Warning: APP_VERSION not found in $env_file${NC}"
        return 1
    fi
    
    echo -e "${YELLOW}Current version in $env_file: $current_version${NC}"
    
    # Increment by 0.01 using awk for floating point arithmetic
    new_version=$(echo "$current_version" | awk '{printf "%.2f", $1 + 0.01}')
    
    # Update the file using sed (works with or without quotes)
    if grep -q "^APP_VERSION=" "$env_file"; then
        # Use sed to replace the version, handling quoted and unquoted values
        # macOS (BSD sed) requires backup extension for sed -i, use empty string for no backup
        # Linux (GNU sed) also accepts empty string, so this works on both systems
        if [[ "$OSTYPE" == "darwin"* ]]; then
            # macOS: requires backup extension (empty string = no backup file)
            sed -i '' "s/^APP_VERSION=.*/APP_VERSION=$new_version/" "$env_file"
        else
            # Linux: GNU sed works with or without backup extension
            sed -i "s/^APP_VERSION=.*/APP_VERSION=$new_version/" "$env_file"
        fi
        echo -e "${GREEN}Updated $env_file: $current_version -> $new_version${NC}"
    else
        echo -e "${RED}Error: Could not find APP_VERSION in $env_file${NC}"
        return 1
    fi
}

# Main execution
echo "=========================================="
echo "Incrementing APP_VERSION by 0.01"
echo "=========================================="
echo ""

# Get the script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Increment version in dashboard/.env
increment_version "$SCRIPT_DIR/.env"

echo ""

echo "=========================================="
echo -e "${GREEN}Version increment completed!${NC}"
echo "=========================================="

