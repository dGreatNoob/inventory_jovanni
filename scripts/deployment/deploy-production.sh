#!/bin/bash
set -e

# Deployment script for production server (GitHub Actions self-hosted runner)
# Runs from repo checkout; operates on PROJECT_DIR (e.g. /var/www/inventory_jovanni)

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="${PROJECT_DIR:-/var/www/inventory_jovanni}"

cd "$PROJECT_DIR" || exit 1

echo "🚀 Starting production deployment..."
echo "   Time: $(date)"
echo "   Directory: $PROJECT_DIR"

# Load environment variables if .env exists
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
fi

# Set defaults for production
DB_CONTAINER="${DB_CONTAINER:-inventory-jovanni-db}"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.prod.yml}"

# Verify compose file exists
if [ ! -f "$COMPOSE_FILE" ]; then
    echo "❌ Error: Compose file '$COMPOSE_FILE' not found in $PROJECT_DIR"
    echo "   Current directory: $(pwd)"
    echo "   Files in directory:"
    ls -la . | head -20
    exit 1
fi
echo "✅ Using compose file: $COMPOSE_FILE"

# Step 1: Backup database (non-blocking)
echo ""
echo "📦 Step 1: Database Backup"
if [ -f "$SCRIPT_DIR/backup-database.sh" ]; then
    DB_CONTAINER="${DB_CONTAINER}" \
    DB_NAME="${DB_DATABASE:-inventory_jovanni}" \
    DB_USER="${DB_USERNAME:-root}" \
    DB_PASSWORD="${DB_PASSWORD:-rootsecret}" \
    bash "$SCRIPT_DIR/backup-database.sh" ./backups || echo "⚠️  Backup failed, but continuing deployment..."
else
    echo "⚠️  Backup script not found, skipping..."
fi

# Step 2: Pull latest code (main branch)
echo ""
echo "📥 Step 2: Pulling latest code (main)..."
if [ -d .git ]; then
    git fetch origin main || echo "⚠️  Git fetch failed, continuing..."
    git checkout main || echo "⚠️  Git checkout failed, continuing..."
    git pull origin main || echo "⚠️  Git pull failed, continuing..."
else
    echo "ℹ️  Not a git repository, skipping git pull"
fi

# Ensure .env file exists and has correct Docker settings
echo ""
echo "🔧 Step 2.5: Verifying .env configuration for Docker..."
if [ ! -f .env ]; then
    echo "⚠️  .env file not found, copying from .env.example..."
    cp .env.example .env || echo "⚠️  .env.example not found either"
fi

# Update DB_HOST to 'db' (Docker service name) if it's set to localhost/127.0.0.1
if [ -f .env ]; then
    # Backup .env
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S) 2>/dev/null || true
    
    # Fix DB_HOST for Docker
    if grep -q "^DB_HOST=127.0.0.1" .env || grep -q "^DB_HOST=localhost" .env; then
        echo "   Updating DB_HOST to 'db' (Docker service name)..."
        sed -i 's/^DB_HOST=127\.0\.0\.1/DB_HOST=db/' .env
        sed -i 's/^DB_HOST=localhost/DB_HOST=db/' .env
    fi
    
    # Ensure DB_PORT is 3306 for Docker (not 3307 which is host port)
    if grep -q "^DB_PORT=3307" .env; then
        echo "   Updating DB_PORT to 3306 (Docker internal port)..."
        sed -i 's/^DB_PORT=3307/DB_PORT=3306/' .env
    fi
    
    # Force DB_USERNAME and DB_PASSWORD to root/rootsecret for Docker
    # (MySQL container always has root; MYSQL_USER is only created on first init, so use root to avoid "Connection refused")
    if grep -q "^DB_USERNAME=" .env; then
        sed -i 's/^DB_USERNAME=.*/DB_USERNAME=root/' .env
        echo "   Set DB_USERNAME=root for Docker MySQL"
    else
        echo "DB_USERNAME=root" >> .env
    fi
    if grep -q "^DB_PASSWORD=" .env; then
        sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=rootsecret/' .env
        echo "   Set DB_PASSWORD=rootsecret for Docker MySQL"
    else
        echo "DB_PASSWORD=rootsecret" >> .env
    fi
    
    # Ensure APP_KEY is set (required to avoid 500 errors)
    if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
        if grep -q "^APP_KEY=$" .env || ! grep -q "^APP_KEY=" .env; then
            echo "   APP_KEY is missing or empty - will be generated when app container starts"
        fi
    fi
    
    # Ensure APP_URL is set for production (helps avoid 500s from URL generation)
    if ! grep -q "^APP_URL=http" .env 2>/dev/null; then
        SERVER_IP=$(hostname -I | awk '{print $1}' 2>/dev/null || echo "localhost")
        echo "   Setting APP_URL=http://$SERVER_IP (update in .env if you use a domain)"
        sed -i "s|^APP_URL=.*|APP_URL=http://$SERVER_IP|" .env 2>/dev/null || true
        if ! grep -q "^APP_URL=" .env; then
            echo "APP_URL=http://$SERVER_IP" >> .env
        fi
    fi
    
    echo "✅ .env configuration verified"
else
    echo "⚠️  .env file not found, Docker environment variables will be used"
fi

# Ensure storage and bootstrap/cache are writable (prevents 500 errors)
echo "   Ensuring storage and cache directories exist..."
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Step 3: Stop existing containers FIRST to free up ports
echo ""
echo "🛑 Step 3: Stopping existing containers..."
cd "$PROJECT_DIR"

# Stop all containers using this compose file
docker compose -f "$COMPOSE_FILE" down --remove-orphans --timeout 30 2>/dev/null || docker-compose -f "$COMPOSE_FILE" down --remove-orphans --timeout 30 2>/dev/null || true

# Force stop any containers with our project name
echo "🔍 Stopping any remaining inventory-jovanni containers..."
docker ps --filter "name=inventory-jovanni" --format "{{.ID}}" | while read -r container_id; do
    if [ -n "$container_id" ]; then
        echo "   Stopping container: $container_id"
        docker stop "$container_id" 2>/dev/null || true
        docker rm -f "$container_id" 2>/dev/null || true
    fi
done

# Check what's using port 80
echo "🔍 Checking what's using port 80..."
if command -v netstat >/dev/null 2>&1; then
    PORT80_PROCESS=$(sudo netstat -tlnp | grep ':80 ' || true)
    if [ -n "$PORT80_PROCESS" ]; then
        echo "⚠️  Port 80 is in use:"
        echo "$PORT80_PROCESS"
    fi
elif command -v ss >/dev/null 2>&1; then
    PORT80_PROCESS=$(sudo ss -tlnp | grep ':80 ' || true)
    if [ -n "$PORT80_PROCESS" ]; then
        echo "⚠️  Port 80 is in use:"
        echo "$PORT80_PROCESS"
    fi
fi

# Try to find and kill process using port 80
if command -v lsof >/dev/null 2>&1; then
    PID=$(sudo lsof -ti:80 2>/dev/null || true)
    if [ -n "$PID" ]; then
        echo "⚠️  Found process $PID using port 80, stopping it..."
        sudo kill -9 "$PID" 2>/dev/null || true
        sleep 3
    fi
fi

# Force remove any containers with our project name that might still exist
docker ps -a --filter "name=inventory-jovanni" --format "{{.ID}}" | xargs -r docker rm -f 2>/dev/null || true

# Verify port 80 is free
if command -v lsof >/dev/null 2>&1; then
    if sudo lsof -ti:80 >/dev/null 2>&1; then
        echo "❌ WARNING: Port 80 is still in use after cleanup attempts!"
        echo "   You may need to manually stop the service using port 80"
    else
        echo "✅ Port 80 is free"
    fi
fi

echo "✅ Container cleanup complete"

# Step 4: Install PHP dependencies inside the app service
echo ""
echo "🧩 Step 4: Installing PHP dependencies via app container (Composer)..."
docker compose -f "$COMPOSE_FILE" run --rm --entrypoint "" app \
  bash -lc "git config --global --add safe.directory /var/www && \
            composer install --no-dev --optimize-autoloader" || {
    echo "❌ Composer install failed inside app container. Aborting deployment."
    exit 1
  }

# Step 5: Build frontend assets inside the app service
echo ""
echo "🎨 Step 5: Building frontend assets via app container (Vite)..."
docker compose -f "$COMPOSE_FILE" run --rm --entrypoint "" app \
  bash -lc "if [ ! -d node_modules ]; then npm ci; fi && npm run build" || {
    echo "❌ Asset build failed inside app container. Aborting deployment."
    exit 1
  }

# Step 6: Build and start containers
echo ""
echo "🔨 Step 6: Building and starting containers..."

# Final check: ensure port 80 is free before starting
if command -v lsof >/dev/null 2>&1; then
    if sudo lsof -ti:80 >/dev/null 2>&1; then
        echo "❌ ERROR: Port 80 is still in use! Cannot start containers."
        echo "   Please manually stop the service using port 80 and try again."
        echo "   Check with: sudo lsof -i:80"
        exit 1
    fi
fi

# Start containers
RUN_MIGRATIONS=true docker compose -f "$COMPOSE_FILE" up -d --build

# Wait a moment for containers to start
sleep 5

# Check initial container status
echo ""
echo "📊 Initial container status:"
docker compose -f "$COMPOSE_FILE" ps

# Step 7: Wait for services to be ready and healthy
echo ""
echo "⏳ Step 7: Waiting for services to be ready..."
echo "   Waiting for database..."
for i in {1..30}; do
    if docker compose -f "$COMPOSE_FILE" exec -T db mysqladmin ping -h localhost -u root -prootsecret >/dev/null 2>&1; then
        echo "   ✅ Database is ready"
        break
    fi
    if [ $i -eq 30 ]; then
        echo "   ⚠️  Database not ready after 30 attempts"
    fi
    sleep 2
done

echo "   Waiting for Redis..."
for i in {1..15}; do
    if docker compose -f "$COMPOSE_FILE" exec -T redis redis-cli ping >/dev/null 2>&1; then
        echo "   ✅ Redis is ready"
        break
    fi
    if [ $i -eq 15 ]; then
        echo "   ⚠️  Redis not ready after 15 attempts"
    fi
    sleep 2
done

echo "   Waiting for app container to be running (not restarting)..."
for i in {1..30}; do
    CONTAINER_STATUS=$(docker inspect --format='{{.State.Status}}' inventory-jovanni-app 2>/dev/null || echo "not-found")
    if [ "$CONTAINER_STATUS" = "running" ]; then
        # Check if it's been running for at least 5 seconds (not restarting)
        sleep 5
        CONTAINER_STATUS=$(docker inspect --format='{{.State.Status}}' inventory-jovanni-app 2>/dev/null || echo "not-found")
        if [ "$CONTAINER_STATUS" = "running" ]; then
            echo "   ✅ App container is running and stable"
            break
        fi
    fi
    if [ $i -eq 30 ]; then
        echo "   ❌ App container failed to start properly"
        echo "   Container logs:"
        docker compose -f "$COMPOSE_FILE" logs --tail=50 app || true
        echo "   ⚠️  Continuing anyway..."
    fi
    sleep 2
done

# Step 8: Run migrations (if not done by entrypoint)
echo ""
echo "📊 Step 8: Running database migrations..."
MAX_RETRIES=5
RETRY_COUNT=0
while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    CONTAINER_STATUS=$(docker inspect --format='{{.State.Status}}' inventory-jovanni-app 2>/dev/null || echo "not-found")
    if [ "$CONTAINER_STATUS" = "running" ]; then
        # Clear config cache first to ensure fresh .env values are used
        echo "   Clearing config cache to pick up .env changes..."
        docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:clear 2>/dev/null || true
        sleep 2
        
        if docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force 2>&1; then
            echo "✅ Migrations completed successfully"
            break
        else
            RETRY_COUNT=$((RETRY_COUNT + 1))
            if [ $RETRY_COUNT -lt $MAX_RETRIES ]; then
                echo "   ⚠️  Migration attempt $RETRY_COUNT failed, retrying in 5 seconds..."
                echo "   Checking database connection..."
                docker compose -f "$COMPOSE_FILE" exec -T app php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';" 2>&1 || echo "   DB connection check failed"
                sleep 5
            else
                echo "   ⚠️  Migrations failed after $MAX_RETRIES attempts"
                echo "   Container logs:"
                docker compose -f "$COMPOSE_FILE" logs --tail=20 app || true
            fi
        fi
    else
        echo "   ⚠️  App container is not running (status: $CONTAINER_STATUS), skipping migrations"
        break
    fi
done

# Step 9: Optimize application and fix common 500-error causes
echo ""
echo "⚡ Step 9: Optimizing application..."
MAX_RETRIES=3
RETRY_COUNT=0
while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    CONTAINER_STATUS=$(docker inspect --format='{{.State.Status}}' inventory-jovanni-app 2>/dev/null || echo "not-found")
    if [ "$CONTAINER_STATUS" = "running" ]; then
        docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:clear 2>/dev/null || true
        
        # Ensure APP_KEY is set (missing key causes 500)
        docker compose -f "$COMPOSE_FILE" exec -T app php artisan key:generate --force 2>/dev/null || true
        
        # Fix storage/cache permissions inside container (www-data)
        docker compose -f "$COMPOSE_FILE" exec -T app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
        docker compose -f "$COMPOSE_FILE" exec -T app chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
        
        if docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:cache 2>&1; then
            docker compose -f "$COMPOSE_FILE" exec -T app php artisan livewire:publish --assets --force 2>/dev/null || true
            docker compose -f "$COMPOSE_FILE" exec -T app php artisan route:cache 2>/dev/null || true
            docker compose -f "$COMPOSE_FILE" exec -T app php artisan view:cache 2>/dev/null || true
            docker compose -f "$COMPOSE_FILE" exec -T app php artisan storage:link 2>/dev/null || true
            echo "✅ Application optimization completed"
            break
        else
            RETRY_COUNT=$((RETRY_COUNT + 1))
            if [ $RETRY_COUNT -lt $MAX_RETRIES ]; then
                echo "   ⚠️  Optimization attempt $RETRY_COUNT failed, retrying in 3 seconds..."
                sleep 3
            else
                echo "   ⚠️  Optimization failed after $MAX_RETRIES attempts, but continuing..."
            fi
        fi
    else
        echo "   ⚠️  App container is not running (status: $CONTAINER_STATUS), skipping optimization"
        break
    fi
done

# Step 10: Health check
echo ""
echo "🏥 Step 10: Health check..."
sleep 5

# Check container status first
CONTAINER_STATUS=$(docker inspect --format='{{.State.Status}}' inventory-jovanni-app 2>/dev/null || echo "not-found")
if [ "$CONTAINER_STATUS" != "running" ]; then
    echo "❌ App container is not running (status: $CONTAINER_STATUS)"
    echo "   Recent container logs:"
    docker compose -f "$COMPOSE_FILE" logs --tail=30 app || true
    echo ""
    echo "   All container status:"
    docker compose -f "$COMPOSE_FILE" ps || true
else
    # Try health check
    if curl -f http://localhost/health 2>/dev/null || curl -f http://localhost 2>/dev/null; then
        echo "✅ Health check passed!"
    else
        echo "⚠️  Health check failed, but container is running"
        echo "   Container logs:"
        docker compose -f "$COMPOSE_FILE" logs --tail=20 app || true
        echo "   Please verify manually"
    fi
fi

# Step 11: Show status and access info
echo ""
echo "📋 Step 11: Container status..."
docker compose -f "$COMPOSE_FILE" ps

# Get server IP for external access
SERVER_IP=$(hostname -I | awk '{print $1}' 2>/dev/null || echo "SERVER_IP")
echo ""
echo "🌐 Access Information:"
echo "   Local:  http://localhost"
echo "   External: http://$SERVER_IP"
echo "   (Ensure firewall allows port 80 if accessing from other devices)"

echo ""
echo "✅ Production deployment completed!"
echo "   Time: $(date)"
echo "   Access: http://localhost"
echo ""
echo "📝 To view logs: docker compose -f $COMPOSE_FILE logs -f"
echo "📝 To restart: docker compose -f $COMPOSE_FILE restart"
