#!/bin/bash

# Fix Composer permissions issue
# Files in vendor/bin/ are owned by root (from Docker), need to be owned by current user

echo "🔧 Fixing Composer vendor directory permissions..."

# Get current user
CURRENT_USER=$(whoami)

# Fix ownership of vendor directory
echo "   Fixing ownership of vendor/ directory..."
sudo chown -R $CURRENT_USER:$CURRENT_USER vendor/ 2>/dev/null || chown -R $CURRENT_USER:$CURRENT_USER vendor/ 2>/dev/null || true

# Fix permissions
echo "   Fixing permissions..."
chmod -R u+w vendor/ 2>/dev/null || true

# Remove problematic files if they still exist
echo "   Cleaning up vendor/bin/..."
rm -f vendor/bin/var-dump-server 2>/dev/null || true
rm -f vendor/bin/* 2>/dev/null || true

# Recreate vendor/bin directory
mkdir -p vendor/bin
chmod 755 vendor/bin

echo "✅ Permissions fixed!"
echo ""
echo "Now try running: composer install"
