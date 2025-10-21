#!/bin/bash

# Laravel Production Permission Fix Script
# This script fixes common permission issues for Laravel applications in production
# Run this script as root or with sudo privileges

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_ROOT="/var/www"
WEB_USER="www-data"
WEB_GROUP="www-data"
LARAVEL_USER="www-data"

echo -e "${BLUE}=== Laravel Production Permission Fix Script ===${NC}"
echo -e "${YELLOW}Project Root: ${PROJECT_ROOT}${NC}"
echo -e "${YELLOW}Web User/Group: ${WEB_USER}:${WEB_GROUP}${NC}"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run this script as root or with sudo${NC}"
    exit 1
fi

# Check if project directory exists
if [ ! -d "$PROJECT_ROOT" ]; then
    echo -e "${RED}Project directory ${PROJECT_ROOT} does not exist${NC}"
    exit 1
fi

cd "$PROJECT_ROOT"

echo -e "${BLUE}Step 1: Setting base ownership and permissions...${NC}"

# Set ownership for entire project
chown -R "$WEB_USER:$WEB_GROUP" "$PROJECT_ROOT"

# Set base permissions for directories
find "$PROJECT_ROOT" -type d -exec chmod 755 {} \;

# Set base permissions for files
find "$PROJECT_ROOT" -type f -exec chmod 644 {} \;

echo -e "${GREEN}✓ Base ownership and permissions set${NC}"

echo -e "${BLUE}Step 2: Setting executable permissions for scripts...${NC}"

# Make artisan executable
chmod +x artisan

# Make shell scripts executable
find "$PROJECT_ROOT" -name "*.sh" -exec chmod +x {} \;

echo -e "${GREEN}✓ Executable permissions set${NC}"

echo -e "${BLUE}Step 3: Creating and fixing Laravel storage directories...${NC}"

# Create necessary storage directories
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/testing
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p storage/app/private
mkdir -p storage/debugbar
mkdir -p bootstrap/cache

echo -e "${GREEN}✓ Storage directories created${NC}"

echo -e "${BLUE}Step 4: Setting Laravel-specific permissions...${NC}"

# Set writable permissions for storage and bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership for storage and cache
chown -R "$WEB_USER:$WEB_GROUP" storage
chown -R "$WEB_USER:$WEB_GROUP" bootstrap/cache

# Set specific permissions for log files
chmod 664 storage/logs/*.log 2>/dev/null || true

echo -e "${GREEN}✓ Laravel-specific permissions set${NC}"

echo -e "${BLUE}Step 5: Setting up storage symlink...${NC}"

# Create storage symlink if it doesn't exist
if [ ! -L "public/storage" ]; then
    if [ -f artisan ]; then
        sudo -u "$WEB_USER" php artisan storage:link
        echo -e "${GREEN}✓ Storage symlink created${NC}"
    else
        echo -e "${YELLOW}⚠ artisan not found, creating symlink manually${NC}"
        ln -sf ../storage/app/public public/storage
        chown "$WEB_USER:$WEB_GROUP" public/storage
        echo -e "${GREEN}✓ Storage symlink created manually${NC}"
    fi
else
    echo -e "${GREEN}✓ Storage symlink already exists${NC}"
fi

echo -e "${BLUE}Step 6: Setting permissions for vendor directory...${NC}"

# Vendor directory should be readable but not writable
if [ -d "vendor" ]; then
    chmod -R 755 vendor
    chown -R "$WEB_USER:$WEB_GROUP" vendor
    echo -e "${GREEN}✓ Vendor permissions set${NC}"
fi

echo -e "${BLUE}Step 7: Setting permissions for node_modules...${NC}"

# Node modules should be readable but not writable
if [ -d "node_modules" ]; then
    chmod -R 755 node_modules
    chown -R "$WEB_USER:$WEB_GROUP" node_modules
    echo -e "${GREEN}✓ Node modules permissions set${NC}"
fi

echo -e "${BLUE}Step 8: Setting permissions for public directory...${NC}"

# Public directory needs to be readable by web server
chmod -R 755 public
chown -R "$WEB_USER:$WEB_GROUP" public

echo -e "${GREEN}✓ Public directory permissions set${NC}"

echo -e "${BLUE}Step 9: Setting up .env file permissions...${NC}"

# .env file should be readable by web server but not by others
if [ -f ".env" ]; then
    chmod 640 .env
    chown "$WEB_USER:$WEB_GROUP" .env
    echo -e "${GREEN}✓ .env file permissions set${NC}"
fi

echo -e "${BLUE}Step 10: Optimizing Laravel application...${NC}"

# Run Laravel optimization commands if artisan exists
if [ -f artisan ]; then
    echo -e "${YELLOW}Running Laravel optimization commands...${NC}"
    
    # Clear and optimize cache
    sudo -u "$WEB_USER" php artisan cache:clear 2>/dev/null || true
    sudo -u "$WEB_USER" php artisan config:clear 2>/dev/null || true
    sudo -u "$WEB_USER" php artisan route:clear 2>/dev/null || true
    sudo -u "$WEB_USER" php artisan view:clear 2>/dev/null || true
    
    # Optimize for production
    sudo -u "$WEB_USER" php artisan config:cache 2>/dev/null || true
    sudo -u "$WEB_USER" php artisan route:cache 2>/dev/null || true
    sudo -u "$WEB_USER" php artisan view:cache 2>/dev/null || true
    
    echo -e "${GREEN}✓ Laravel optimization completed${NC}"
else
    echo -e "${YELLOW}⚠ artisan not found, skipping optimization${NC}"
fi

echo -e "${BLUE}Step 11: Final permission verification...${NC}"

# Verify critical permissions
echo -e "${YELLOW}Checking critical directories:${NC}"

# Check storage permissions
if [ -d "storage" ]; then
    STORAGE_PERMS=$(stat -c "%a" storage)
    if [ "$STORAGE_PERMS" = "775" ]; then
        echo -e "${GREEN}✓ storage: $STORAGE_PERMS${NC}"
    else
        echo -e "${RED}✗ storage: $STORAGE_PERMS (should be 775)${NC}"
    fi
fi

# Check bootstrap/cache permissions
if [ -d "bootstrap/cache" ]; then
    CACHE_PERMS=$(stat -c "%a" bootstrap/cache)
    if [ "$CACHE_PERMS" = "775" ]; then
        echo -e "${GREEN}✓ bootstrap/cache: $CACHE_PERMS${NC}"
    else
        echo -e "${RED}✗ bootstrap/cache: $CACHE_PERMS (should be 775)${NC}"
    fi
fi

# Check artisan permissions
if [ -f "artisan" ]; then
    ARTISAN_PERMS=$(stat -c "%a" artisan)
    if [ "$ARTISAN_PERMS" = "755" ]; then
        echo -e "${GREEN}✓ artisan: $ARTISAN_PERMS${NC}"
    else
        echo -e "${RED}✗ artisan: $ARTISAN_PERMS (should be 755)${NC}"
    fi
fi

echo ""
echo -e "${GREEN}=== Permission Fix Complete ===${NC}"
echo -e "${YELLOW}Summary:${NC}"
echo -e "  • Set ownership to ${WEB_USER}:${WEB_GROUP}"
echo -e "  • Set storage directory to 775 (writable)"
echo -e "  • Set bootstrap/cache to 775 (writable)"
echo -e "  • Set other directories to 755 (readable)"
echo -e "  • Set files to 644 (readable)"
echo -e "  • Made scripts executable (755)"
echo -e "  • Created storage symlink"
echo -e "  • Optimized Laravel application"
echo ""
echo -e "${BLUE}If you're still experiencing permission issues:${NC}"
echo -e "  1. Check your web server configuration"
echo -e "  2. Verify SELinux/AppArmor settings if enabled"
echo -e "  3. Check if your web server user matches ${WEB_USER}"
echo -e "  4. Ensure the web server has access to the project directory"
echo ""
echo -e "${GREEN}Script completed successfully!${NC}"
