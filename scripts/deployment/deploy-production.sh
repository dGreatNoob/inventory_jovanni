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

# Step 3: Install PHP dependencies inside the app service
echo ""
echo "🧩 Step 3: Installing PHP dependencies via app container (Composer)..."
docker compose -f "$COMPOSE_FILE" run --rm --entrypoint "" app \
  bash -lc "git config --global --add safe.directory /var/www && \
            composer install --no-dev --optimize-autoloader" || {
    echo "❌ Composer install failed inside app container. Aborting deployment."
    exit 1
  }

# Step 4: Build frontend assets inside the app service
echo ""
echo "🎨 Step 4: Building frontend assets via app container (Vite)..."
docker compose -f "$COMPOSE_FILE" run --rm --entrypoint "" app \
  bash -lc "if [ ! -d node_modules ]; then npm ci; fi && npm run build" || {
    echo "❌ Asset build failed inside app container. Aborting deployment."
    exit 1
  }

# Step 5: Stop containers gracefully
echo ""
echo "🛑 Step 5: Stopping containers..."
docker compose -f "$COMPOSE_FILE" down || docker-compose -f "$COMPOSE_FILE" down || true

# Step 6: Build and start containers
echo ""
echo "🔨 Step 6: Building and starting containers..."
RUN_MIGRATIONS=true docker compose -f "$COMPOSE_FILE" up -d --build

# Step 7: Wait for services to be ready
echo ""
echo "⏳ Step 7: Waiting for services to be ready..."
sleep 15

# Step 8: Run migrations (if not done by entrypoint)
echo ""
echo "📊 Step 8: Running database migrations..."
if docker ps | grep -q "inventory-jovanni-app"; then
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force || echo "⚠️  Migrations failed"
else
    echo "⚠️  App container not running, skipping migrations"
fi

# Step 9: Optimize application
echo ""
echo "⚡ Step 9: Optimizing application..."
if docker ps | grep -q "inventory-jovanni-app"; then
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:clear || true
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:cache || true
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan route:cache || true
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan view:cache || true
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan storage:link || true
else
    echo "⚠️  App container not running, skipping optimization"
fi

# Step 10: Health check
echo ""
echo "🏥 Step 10: Health check..."
sleep 5

if curl -f http://localhost/health 2>/dev/null || curl -f http://localhost 2>/dev/null; then
    echo "✅ Health check passed!"
else
    echo "⚠️  Health check failed, but deployment completed"
    echo "   Please verify manually: http://localhost"
fi

# Step 11: Show status
echo ""
echo "📋 Step 11: Container status..."
docker compose -f "$COMPOSE_FILE" ps

echo ""
echo "✅ Production deployment completed!"
echo "   Time: $(date)"
echo "   Access: http://localhost"
echo ""
echo "📝 To view logs: docker compose -f $COMPOSE_FILE logs -f"
echo "📝 To restart: docker compose -f $COMPOSE_FILE restart"
