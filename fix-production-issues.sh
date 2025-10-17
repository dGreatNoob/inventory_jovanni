#!/bin/bash

echo "=== Laravel Production Fix Script ==="
echo "This script will attempt to fix common Laravel Docker issues"
echo ""

# Check if we're in the right directory
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ Error: docker-compose.yml not found. Please run this script from the project root directory."
    exit 1
fi

echo "✅ Found docker-compose.yml"

# Stop all containers
echo ""
echo "=== Stopping all containers ==="
docker-compose down

# Remove any existing containers and volumes (be careful with this in production)
echo ""
echo "=== Cleaning up containers and volumes ==="
docker-compose down -v
docker system prune -f

# Rebuild the containers
echo ""
echo "=== Rebuilding containers ==="
docker-compose build --no-cache

# Start the containers
echo ""
echo "=== Starting containers ==="
docker-compose up -d

# Wait for containers to be ready
echo ""
echo "=== Waiting for containers to be ready ==="
sleep 15

# Check container status
echo ""
echo "=== Container Status ==="
docker-compose ps

# Install dependencies
echo ""
echo "=== Installing PHP dependencies ==="
docker-compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate application key
echo ""
echo "=== Generating application key ==="
docker-compose exec app php artisan key:generate

# Clear caches
echo ""
echo "=== Clearing caches ==="
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Set proper permissions
echo ""
echo "=== Setting permissions ==="
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chmod -R 775 /var/www/storage
docker-compose exec app chmod -R 775 /var/www/bootstrap/cache

# Run migrations
echo ""
echo "=== Running migrations ==="
docker-compose exec app php artisan migrate --force

# Test the application
echo ""
echo "=== Testing application ==="
echo "Testing database connection..."
docker-compose exec app php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection successful';"

echo ""
echo "Testing web access..."
curl -I http://localhost || echo "Web access test failed"

echo ""
echo "=== Fix Complete ==="
echo "Your application should now be accessible at:"
echo "- Main application: http://localhost"
echo "- phpMyAdmin: http://localhost:8082"
echo "- Database: localhost:3307"
echo "- Redis: localhost:6380"
