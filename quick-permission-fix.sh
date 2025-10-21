#!/bin/bash

# Quick Laravel Permission Fix
# Use this for quick permission fixes without the full optimization

set -e

PROJECT_ROOT="/var/www"
WEB_USER="www-data"
WEB_GROUP="www-data"

echo "Quick Laravel Permission Fix"
echo "Project: $PROJECT_ROOT"

cd "$PROJECT_ROOT"

# Essential permission fixes
chown -R "$WEB_USER:$WEB_GROUP" "$PROJECT_ROOT"
chmod -R 775 storage bootstrap/cache
chmod +x artisan

# Create storage symlink if needed
if [ ! -L "public/storage" ] && [ -f artisan ]; then
    sudo -u "$WEB_USER" php artisan storage:link
fi

echo "Quick fix completed!"
