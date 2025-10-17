#!/bin/bash

# Quick Deployment Script for Production Server
# Run this script to quickly deploy the application

set -e

echo "=========================================="
echo "  Inventory Jovanni - Quick Deploy"
echo "=========================================="
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "Creating .env file from example..."
    cp .env.example .env
    echo "✅ .env file created"
else
    echo "✅ .env file already exists"
fi

# Install PHP dependencies
echo "Installing PHP dependencies..."
php -d memory_limit=2G /usr/local/bin/composer install --no-dev --optimize-autoloader --no-scripts

# Run post-install scripts
echo "Running post-install scripts..."
php artisan package:discover --ansi

# Install Node.js dependencies
echo "Installing Node.js dependencies..."
npm install

# Build assets
echo "Building frontend assets..."
npm run build

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    php artisan key:generate --ansi
fi

# Set permissions
echo "Setting permissions..."
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "=========================================="
echo "✅ Quick deployment completed!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Update .env file with your database credentials"
echo "2. Run: php artisan migrate"
echo "3. Configure your web server"
echo "4. Set up SSL certificate"
echo ""
