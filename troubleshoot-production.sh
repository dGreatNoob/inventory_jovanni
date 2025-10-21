#!/bin/bash

echo "=== Laravel Production Troubleshooting Script ==="
echo "This script will help diagnose common issues with Laravel in Docker"
echo ""

# Check if we're in the right directory
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ Error: docker-compose.yml not found. Please run this script from the project root directory."
    exit 1
fi

echo "✅ Found docker-compose.yml"

# Check if containers are running
echo ""
echo "=== Checking Container Status ==="
docker-compose ps

echo ""
echo "=== Checking App Container Health ==="
if docker-compose ps | grep -q "inventory-jovanni-app.*Up"; then
    echo "✅ App container is running"
    
    # Check if vendor directory exists
    echo ""
    echo "=== Checking Dependencies ==="
    if docker-compose exec app ls -la vendor > /dev/null 2>&1; then
        echo "✅ Vendor directory exists"
    else
        echo "❌ Vendor directory missing - running composer install"
        docker-compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader
    fi
    
    # Check PHP extensions
    echo ""
    echo "=== Checking PHP Extensions ==="
    docker-compose exec app php -m | grep -E "(pdo|mysql|mbstring|xml|zip|gd|curl)"
    
    # Check Laravel configuration
    echo ""
    echo "=== Checking Laravel Configuration ==="
    echo "App Key:"
    docker-compose exec app php artisan key:generate --show
    
    echo ""
    echo "Environment:"
    docker-compose exec app php artisan env
    
    # Check database connection
    echo ""
    echo "=== Testing Database Connection ==="
    docker-compose exec app php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection successful';"
    
    # Check file permissions
    echo ""
    echo "=== Checking File Permissions ==="
    docker-compose exec app ls -la storage/
    docker-compose exec app ls -la bootstrap/cache/
    
    # Try to run migrations
    echo ""
    echo "=== Testing Migrations ==="
    docker-compose exec app php artisan migrate:status
    
else
    echo "❌ App container is not running. Starting containers..."
    docker-compose up -d
    sleep 10
    echo "Retrying checks..."
    docker-compose exec app php artisan --version
fi

echo ""
echo "=== Troubleshooting Complete ==="
echo "If you're still experiencing issues, please check:"
echo "1. Docker and Docker Compose are properly installed"
echo "2. Ports 80, 3307, 6379, 8082 are not in use by other services"
echo "3. You have sufficient disk space and memory"
echo "4. The .env file is properly configured"
