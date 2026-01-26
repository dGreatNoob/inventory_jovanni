#!/bin/bash

# Docker Deployment Start Script
# This script helps you quickly deploy the Inventory Jovanni application

set -e

echo "🚀 Inventory Jovanni Docker Deployment"
echo "======================================"
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE_CMD="docker-compose"
elif docker compose version &> /dev/null; then
    DOCKER_COMPOSE_CMD="docker compose"
else
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Check if .env file exists
if [ ! -f .env ]; then
    echo "⚠️  .env file not found. Creating from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✅ Created .env file. Please edit it with your configuration."
    else
        echo "❌ .env.example not found. Please create .env manually."
        exit 1
    fi
fi

# Set permissions on host (before containers start)
echo "📁 Setting permissions on host..."
# Create necessary directories if they don't exist
mkdir -p storage/framework/cache/data 2>/dev/null || true
mkdir -p storage/framework/sessions 2>/dev/null || true
mkdir -p storage/framework/views 2>/dev/null || true
mkdir -p storage/logs 2>/dev/null || true
mkdir -p storage/app/public 2>/dev/null || true
mkdir -p bootstrap/cache 2>/dev/null || true

# Set permissions for storage and cache directories
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
# Ensure storage subdirectories have correct permissions
find storage -type d -exec chmod 775 {} \; 2>/dev/null || true
find storage -type f -exec chmod 664 {} \; 2>/dev/null || true
find bootstrap/cache -type d -exec chmod 775 {} \; 2>/dev/null || true
find bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null || true
echo "      ✅ Host permissions set"

# Determine deployment type from arguments or default to development
COMPOSE_FILE="docker-compose.yml"
DEPLOYMENT_TYPE="development"

if [ "$1" = "--help" ] || [ "$1" = "-h" ]; then
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --dev, --development    Use development configuration (default)"
    echo "  --prod, --production   Use production configuration"
    echo "  --help, -h              Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0                      # Start with development config"
    echo "  $0 --dev                # Start with development config"
    echo "  $0 --prod               # Start with production config"
    exit 0
elif [ "$1" = "--prod" ] || [ "$1" = "--production" ]; then
    COMPOSE_FILE="docker-compose.prod.yml"
    DEPLOYMENT_TYPE="production"
elif [ "$1" = "--dev" ] || [ "$1" = "--development" ]; then
    COMPOSE_FILE="docker-compose.yml"
    DEPLOYMENT_TYPE="development"
elif [ -n "$1" ]; then
    echo "⚠️  Unknown argument: $1"
    echo "Usage: $0 [--dev|--prod|--help]"
    echo "Defaulting to development..."
fi

echo ""
echo "📦 Deployment type: $DEPLOYMENT_TYPE"
echo "📄 Using compose file: $COMPOSE_FILE"

echo ""
echo "🔨 Building and starting containers..."
# Export APP_URL and ASSET_URL if they were set above (for environment variable injection)
if [ -n "$APP_URL" ] && [ "$APP_URL" != "localhost" ]; then
    export APP_URL
    export ASSET_URL
fi
if ! $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE up -d --build; then
    echo "❌ Failed to start containers. Check the error messages above."
    exit 1
fi

echo ""
echo "⏳ Waiting for services to be ready..."
sleep 15

# Check if containers are running
if ! $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE ps 2>/dev/null | grep -q "Up"; then
    echo "⚠️  Some containers may not be running. Check logs with: $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE logs"
    exit 1
fi

echo "✅ Containers are running!"

# Wait for app container to be fully ready
echo "⏳ Waiting for application container to be ready..."
# Wait for container to be running (not restarting)
MAX_WAIT=60
WAIT_COUNT=0
while [ $WAIT_COUNT -lt $MAX_WAIT ]; do
    CONTAINER_STATUS=$($DOCKER_COMPOSE_CMD -f $COMPOSE_FILE ps app 2>/dev/null | grep "app" | grep -o "Up\|Restarting" | head -1 || echo "")
    if [ "$CONTAINER_STATUS" = "Up" ]; then
        echo "      ✅ Container is running"
        break
    fi
    if [ "$CONTAINER_STATUS" = "Restarting" ]; then
        echo "      ⏳ Container is restarting, waiting..."
        sleep 2
        WAIT_COUNT=$((WAIT_COUNT + 2))
    else
        sleep 1
        WAIT_COUNT=$((WAIT_COUNT + 1))
    fi
done
if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
    echo "      ⚠️  Container may not be ready. Continuing anyway..."
fi
sleep 3

# Run setup steps
echo ""
echo "🔧 Running application setup..."

# Check if app key exists, if not generate it
echo "  1️⃣  Checking application key..."
if ! $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan key:generate --show 2>&1 | grep -q "base64:"; then
    echo "      Generating application key..."
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan key:generate --force > /dev/null 2>&1 || true
else
    echo "      ✅ Application key already exists"
fi

# Run migrations with better error handling
echo "  2️⃣  Running database migrations..."
# Wait for database to be fully ready with retry logic
echo "      Checking database connection..."
DB_READY=false
MAX_RETRIES=10
RETRY_COUNT=0
while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    # Check if container is running first
    CONTAINER_STATUS=$($DOCKER_COMPOSE_CMD -f $COMPOSE_FILE ps app 2>/dev/null | grep "app" | grep -o "Up\|Restarting" | head -1 || echo "")
    if [ "$CONTAINER_STATUS" != "Up" ]; then
        echo "      ⏳ Waiting for container to be ready..."
        sleep 2
        RETRY_COUNT=$((RETRY_COUNT + 1))
        continue
    fi
    
    # Try database connection
    if $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan db:show > /dev/null 2>&1; then
        DB_READY=true
        echo "      ✅ Database connection established"
        break
    else
        echo "      ⏳ Retrying database connection ($((RETRY_COUNT + 1))/$MAX_RETRIES)..."
        sleep 2
        RETRY_COUNT=$((RETRY_COUNT + 1))
    fi
done

if [ "$DB_READY" = "true" ]; then
    # Run migrations
    if $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan migrate --force > /dev/null 2>&1; then
        echo "      ✅ Migrations completed"
        
        # Run seeders only if database is empty (first run)
        echo "  2️⃣.1️⃣  Checking if seeders are needed..."
        USER_COUNT=$($DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1 | tr -d '\r\n' || echo "0")
        if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ] || [ "$USER_COUNT" = "" ]; then
            echo "      Running database seeders..."
            $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan db:seed --force > /dev/null 2>&1 || echo "      ⚠️  Seeding may have issues"
            echo "      ✅ Seeders completed"
        else
            echo "      ⏭️  Database already seeded (users exist)"
        fi
    else
        echo "      ⚠️  Migration may have failed or already run. Check logs if needed."
    fi
else
    echo "      ⚠️  Cannot connect to database after $MAX_RETRIES attempts."
    echo "      ⚠️  Please check database configuration and ensure containers are running."
    echo "      ⚠️  You can check logs with: $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE logs db"
fi

# Create storage link and ensure proper setup
echo "  3️⃣  Creating storage symlink and ensuring directories exist..."
# Remove existing symlink if it's broken or incorrect
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app rm -f /var/www/public/storage > /dev/null 2>&1 || true
# Create storage link
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan storage:link > /dev/null 2>&1 || true
# Ensure storage subdirectories exist
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app mkdir -p /var/www/storage/framework/cache/data > /dev/null 2>&1 || true
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app mkdir -p /var/www/storage/framework/sessions > /dev/null 2>&1 || true
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app mkdir -p /var/www/storage/framework/views > /dev/null 2>&1 || true
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app mkdir -p /var/www/storage/logs > /dev/null 2>&1 || true
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app mkdir -p /var/www/storage/app/public > /dev/null 2>&1 || true
echo "      ✅ Storage link and directories created"

# Build assets if not present
echo "  4️⃣  Checking and building assets..."
# Wait for container to be ready before checking assets
CONTAINER_STATUS=$($DOCKER_COMPOSE_CMD -f $COMPOSE_FILE ps app 2>/dev/null | grep "app" | grep -o "Up\|Restarting" | head -1 || echo "")
if [ "$CONTAINER_STATUS" = "Up" ]; then
    # Check if manifest exists in container
    MANIFEST_EXISTS=$($DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app test -f /var/www/public/build/manifest.json 2>/dev/null && echo "yes" || echo "no")
    if [ "$MANIFEST_EXISTS" != "yes" ] && [ ! -f "public/build/manifest.json" ]; then
        echo "      Building frontend assets..."
        # Ensure node_modules exists (check in container)
        if ! $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app test -d /var/www/node_modules > /dev/null 2>&1; then
            echo "      Installing npm dependencies..."
            $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app npm ci --only=production > /dev/null 2>&1 || true
        fi
        $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app npm run build > /dev/null 2>&1 || echo "      ⚠️  Asset build may have issues"
        echo "      ✅ Assets built"
    else
        echo "      ✅ Assets already built"
    fi
else
    echo "      ⚠️  Container is not ready. Skipping asset check."
    echo "      ✅ Assets check skipped"
fi

# Set proper permissions inside container
echo "  5️⃣  Setting file permissions inside container..."
# Check if container is ready
CONTAINER_STATUS=$($DOCKER_COMPOSE_CMD -f $COMPOSE_FILE ps app 2>/dev/null | grep "app" | grep -o "Up\|Restarting" | head -1 || echo "")
if [ "$CONTAINER_STATUS" = "Up" ]; then
    # Set ownership for all storage and cache directories
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app chown -R www-data:www-data /var/www/storage > /dev/null 2>&1 || true
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app chown -R www-data:www-data /var/www/bootstrap/cache > /dev/null 2>&1 || true
    # Set directory permissions
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app find /var/www/storage -type d -exec chmod 775 {} \; > /dev/null 2>&1 || true
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app find /var/www/bootstrap/cache -type d -exec chmod 775 {} \; > /dev/null 2>&1 || true
    # Set file permissions
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app find /var/www/storage -type f -exec chmod 664 {} \; > /dev/null 2>&1 || true
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app find /var/www/bootstrap/cache -type f -exec chmod 664 {} \; > /dev/null 2>&1 || true
    # Ensure public/storage symlink has correct permissions if it exists
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app chown -h www-data:www-data /var/www/public/storage > /dev/null 2>&1 || true
    echo "      ✅ Container permissions set"
else
    echo "      ⚠️  Container is not ready. Permissions will be set when container is running."
fi

# Cache configuration (only after ensuring APP_URL is correct)
echo "  6️⃣  Caching configuration..."
# Check if container is ready
CONTAINER_STATUS=$($DOCKER_COMPOSE_CMD -f $COMPOSE_FILE ps app 2>/dev/null | grep "app" | grep -o "Up\|Restarting" | head -1 || echo "")
if [ "$CONTAINER_STATUS" = "Up" ]; then
    # Force clear config first to ensure fresh read from .env
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan config:clear > /dev/null 2>&1 || true
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php bootstrap/cache/*.php > /dev/null 2>&1 || true
    # Restart app to ensure .env is reloaded
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE restart app > /dev/null 2>&1
    sleep 5
    # Wait for container to be ready after restart
    WAIT_COUNT=0
    while [ $WAIT_COUNT -lt 30 ]; do
        CONTAINER_STATUS=$($DOCKER_COMPOSE_CMD -f $COMPOSE_FILE ps app 2>/dev/null | grep "app" | grep -o "Up\|Restarting" | head -1 || echo "")
        if [ "$CONTAINER_STATUS" = "Up" ]; then
            break
        fi
        sleep 1
        WAIT_COUNT=$((WAIT_COUNT + 1))
    done
    # Verify APP_URL is correct before caching
    CURRENT_APP_URL=$($DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan tinker --execute="echo env('APP_URL');" 2>/dev/null | tail -1 | tr -d '\r\n' || echo "")
    if [ -n "$CURRENT_APP_URL" ] && [ "$CURRENT_APP_URL" != "http://localhost" ] && [ "$CURRENT_APP_URL" != "" ]; then
        echo "      ✅ APP_URL is correctly set to: $CURRENT_APP_URL"
    else
        echo "      ⚠️  APP_URL may need manual configuration in .env file"
    fi
    # Now cache with correct values from .env
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan config:cache > /dev/null 2>&1 || true
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan route:cache > /dev/null 2>&1 || true
    $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T app php artisan view:cache > /dev/null 2>&1 || true
    echo "      ✅ Configuration cached"
else
    echo "      ⚠️  Container is not ready. Configuration caching skipped."
fi

# Get server IP and ensure APP_URL and ASSET_URL are set correctly
# Try multiple methods to get server IP
SERVER_IP=$(hostname -I 2>/dev/null | awk '{print $1}')
if [ -z "$SERVER_IP" ] || [ "$SERVER_IP" = "" ]; then
    SERVER_IP=$(ip addr show 2>/dev/null | grep "inet " | grep -v "127.0.0.1" | head -1 | awk '{print $2}' | cut -d'/' -f1)
fi
if [ -z "$SERVER_IP" ] || [ "$SERVER_IP" = "" ]; then
    SERVER_IP="localhost"
fi
if [ -f .env ] && [ -n "$SERVER_IP" ] && [ "$SERVER_IP" != "localhost" ]; then
    # Update APP_URL and ASSET_URL in .env to use server IP
    echo "  7️⃣  Ensuring APP_URL and ASSET_URL are set correctly..."
    if grep -q "^APP_URL=" .env 2>/dev/null; then
        sed -i "s|^APP_URL=.*|APP_URL=http://$SERVER_IP|" .env
    else
        echo "APP_URL=http://$SERVER_IP" >> .env
    fi
    # Set ASSET_URL to same as APP_URL for proper asset loading
    if grep -q "^ASSET_URL=" .env 2>/dev/null; then
        sed -i "s|^ASSET_URL=.*|ASSET_URL=http://$SERVER_IP|" .env
    else
        echo "ASSET_URL=http://$SERVER_IP" >> .env
    fi
    echo "      ✅ APP_URL and ASSET_URL set to http://$SERVER_IP"
    # Export for docker-compose to use as environment variable
    export APP_URL="http://$SERVER_IP"
    export ASSET_URL="http://$SERVER_IP"
fi

echo ""
echo "======================================"
echo "✅ Deployment Complete!"
echo ""
echo "Application URLs:"
echo "  - Local: http://localhost"
echo "  - Network: http://$SERVER_IP"
echo ""
echo "Other Services:"
echo "  - phpMyAdmin: http://$SERVER_IP:8081"
echo ""
echo "Useful Commands:"
echo "  View logs: $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE logs -f"
echo "  Stop services: $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE down"
echo "  Restart services: $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE restart"
echo "======================================"
