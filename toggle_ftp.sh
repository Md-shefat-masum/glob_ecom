#!/bin/bash

# Quick FTP Toggle Script
# Easily enable/disable FTP auto-upload

CONFIG_FILE=".vscode/sftp.json"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  🔄 FTP Auto-Upload Toggle"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Check if config exists
if [ ! -f "$CONFIG_FILE" ]; then
    echo -e "${RED}❌ Config file not found: $CONFIG_FILE${NC}"
    exit 1
fi

# Check current status
CURRENT_STATUS=$(grep '"uploadOnSave"' "$CONFIG_FILE" | grep -o 'true\|false')

echo "📊 Current Status:"
if [ "$CURRENT_STATUS" = "true" ]; then
    echo -e "   ${GREEN}✅ FTP Auto-upload is ENABLED${NC}"
    echo ""
    echo "What do you want to do?"
    echo "  1) Turn OFF (disable auto-upload)"
    echo "  2) Keep enabled"
    echo ""
    read -p "Choose (1/2): " choice
    
    if [ "$choice" = "1" ]; then
        # Turn OFF
        sed -i 's/"uploadOnSave": true/"uploadOnSave": false/g' "$CONFIG_FILE"
        sed -i 's/"autoUpload": true/"autoUpload": false/g' "$CONFIG_FILE"
        echo ""
        echo -e "${YELLOW}🔴 FTP Auto-upload DISABLED${NC}"
        echo ""
        echo "Files will NOT auto-upload on save."
        echo "Use manual upload when needed:"
        echo "  Right-click → SFTP: Upload"
    else
        echo ""
        echo -e "${GREEN}✅ Keeping FTP enabled${NC}"
    fi
else
    echo -e "   ${RED}🔴 FTP Auto-upload is DISABLED${NC}"
    echo ""
    echo "What do you want to do?"
    echo "  1) Turn ON (enable auto-upload)"
    echo "  2) Keep disabled"
    echo ""
    read -p "Choose (1/2): " choice
    
    if [ "$choice" = "1" ]; then
        # Turn ON
        sed -i 's/"uploadOnSave": false/"uploadOnSave": true/g' "$CONFIG_FILE"
        sed -i 's/"autoUpload": false/"autoUpload": true/g' "$CONFIG_FILE"
        echo ""
        echo -e "${GREEN}🟢 FTP Auto-upload ENABLED${NC}"
        echo ""
        echo "Files will auto-upload on save!"
        echo "Be careful - changes go live immediately!"
    else
        echo ""
        echo -e "${YELLOW}✅ Keeping FTP disabled${NC}"
    fi
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${YELLOW}⚠️  Remember to reload Cursor!${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Ctrl+Shift+P → 'Developer: Reload Window'"
echo ""

