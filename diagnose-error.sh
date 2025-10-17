#!/bin/bash

echo "=== Laravel Error Diagnosis ==="
echo "This script will help diagnose the specific error you're encountering"
echo ""

# Check if containers are running
if ! docker-compose ps | grep -q "inventory-jovanni-app.*Up"; then
    echo "❌ App container is not running. Starting it first..."
    docker-compose up -d
    sleep 10
fi

echo "=== 1. Checking PHP Version and Extensions ==="
docker-compose exec app php --version
echo ""
docker-compose exec app php -m | grep -E "(pdo|mysql|mbstring|xml|zip|gd|curl|openssl|json|tokenizer|fileinfo|ctype|bcmath)"

echo ""
echo "=== 2. Checking Composer Dependencies ==="
docker-compose exec app composer --version
echo ""
docker-compose exec app ls -la vendor/ | head -10

echo ""
echo "=== 3. Checking Laravel Configuration ==="
docker-compose exec app php artisan --version
echo ""
docker-compose exec app cat .env | head -10

echo ""
echo "=== 4. Testing Specific Commands ==="
echo "Testing artisan commands:"
docker-compose exec app php artisan list | head -5

echo ""
echo "Testing database connection:"
docker-compose exec app php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database OK'; } catch (Exception \$e) { echo 'Database Error: ' . \$e->getMessage(); }"

echo ""
echo "=== 5. Checking File Permissions ==="
docker-compose exec app ls -la storage/
docker-compose exec app ls -la bootstrap/cache/

echo ""
echo "=== 6. Testing Migration Command ==="
echo "Running migration status:"
docker-compose exec app php artisan migrate:status

echo ""
echo "=== 7. Checking Error Logs ==="
echo "Laravel logs:"
docker-compose exec app tail -20 storage/logs/laravel.log 2>/dev/null || echo "No Laravel logs found"

echo ""
echo "PHP-FPM logs:"
docker-compose logs app --tail=10

echo ""
echo "=== Diagnosis Complete ==="
echo "If you see any errors above, please share them for further troubleshooting."
