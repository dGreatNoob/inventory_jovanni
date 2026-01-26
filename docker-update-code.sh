#!/bin/bash

# Docker Code Update Script
# This script updates the code and ensures changes are reflected in the running containers

set -e

echo "🔄 Docker Code Update Script"
echo "============================"
echo ""

# Check if Docker Compose is installed
if command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE_CMD="docker-compose"
elif docker compose version &> /dev/null; then
    DOCKER_COMPOSE_CMD="docker compose"
else
    echo "❌ Docker Compose is not installed."
    exit 1
fi

# Determine compose file from argument or default to production
COMPOSE_FILE="docker-compose.prod.yml"
if [ "$1" = "--dev" ] || [ "$1" = "--development" ]; then
    COMPOSE_FILE="docker-compose.yml"
elif [ "$1" = "--prod" ] || [ "$1" = "--production" ]; then
    COMPOSE_FILE="docker-compose.prod.yml"
fi

echo "📦 Using compose file: $COMPOSE_FILE"
echo ""

# Step 1: Pull latest code (if in git repo)
if [ -d .git ]; then
    echo "1️⃣  Pulling latest code from git..."
    git pull 2>&1 || echo "   ⚠️  Git pull may have issues or not needed"
    echo "   ✅ Code updated"
else
    echo "1️⃣  Not a git repository, skipping git pull"
fi

# Step 2: Clear Laravel caches
echo ""
echo "2️⃣  Clearing Laravel caches..."
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan config:clear > /dev/null 2>&1 || true
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan route:clear > /dev/null 2>&1 || true
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan view:clear > /dev/null 2>&1 || true
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan cache:clear > /dev/null 2>&1 || true
echo "   ✅ Caches cleared"

# Step 3: Clear OPcache (PHP opcode cache)
echo ""
echo "3️⃣  Clearing PHP OPcache..."
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan opcache:clear > /dev/null 2>&1 || \
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php -r "if(function_exists('opcache_reset')) opcache_reset();" > /dev/null 2>&1 || true
echo "   ✅ OPcache cleared"

# Step 4: Restart containers to ensure code changes are loaded
echo ""
echo "4️⃣  Restarting application container..."
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE restart app > /dev/null 2>&1 || true
sleep 5
echo "   ✅ Container restarted"

# Step 5: Rebuild config cache
echo ""
echo "5️⃣  Rebuilding configuration cache..."
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan config:cache > /dev/null 2>&1 || true
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan route:cache > /dev/null 2>&1 || true
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan view:cache > /dev/null 2>&1 || true
echo "   ✅ Configuration cached"

echo ""
echo "======================================"
echo "✅ Code update complete!"
echo ""
echo "Your code changes should now be active."
echo "======================================"
