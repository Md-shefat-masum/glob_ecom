#!/bin/bash

# FTP Setup Script for New Projects
# Quick setup FTP config for any new project

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  🚀 FTP Setup for New Project"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Get project path
read -p "📂 Enter new project path: " PROJECT_PATH

# Check if path exists
if [ ! -d "$PROJECT_PATH" ]; then
    echo "❌ Error: Project path not found!"
    exit 1
fi

# Create .vscode directory if not exists
mkdir -p "$PROJECT_PATH/.vscode"

# Get project name
read -p "📝 Enter project name: " PROJECT_NAME

# Get remote path
read -p "🌐 Enter remote path (e.g., / or /subfolder): " REMOTE_PATH

# Use default if empty
if [ -z "$REMOTE_PATH" ]; then
    REMOTE_PATH="/"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📋 Configuration:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Project: $PROJECT_NAME"
echo "Path: $PROJECT_PATH"
echo "Remote: $REMOTE_PATH"
echo "Host: teesta-bd-cp4.hostever.us"
echo "User: posapp@bme.com.bd"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

read -p "✅ Proceed with setup? (y/n): " CONFIRM

if [ "$CONFIRM" != "y" ]; then
    echo "❌ Setup cancelled"
    exit 0
fi

# Copy template
TEMPLATE_PATH="/home/siyam/Desktop/wwwroot/production_projects/bme_pos/.vscode/sftp.json.template"
CONFIG_PATH="$PROJECT_PATH/.vscode/sftp.json"

if [ ! -f "$TEMPLATE_PATH" ]; then
    echo "❌ Template not found! Using current config..."
    TEMPLATE_PATH="/home/siyam/Desktop/wwwroot/production_projects/bme_pos/.vscode/sftp.json"
fi

cp "$TEMPLATE_PATH" "$CONFIG_PATH"

# Update config
sed -i "s|PROJECT_NAME_HERE|$PROJECT_NAME|g" "$CONFIG_PATH"
sed -i "s|\"remotePath\": \"/\"|\"remotePath\": \"$REMOTE_PATH\"|g" "$CONFIG_PATH"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ FTP Config Created Successfully!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📁 Config file: $CONFIG_PATH"
echo ""
echo "🎯 Next steps:"
echo "   1. Open project in Cursor"
echo "   2. Ctrl+Shift+P → 'SFTP: List'"
echo "   3. Start coding!"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

