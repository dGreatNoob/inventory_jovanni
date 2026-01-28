#!/bin/bash
set -e

# Deployment script for staging server
# This script is run by the GitHub Actions self-hosted runner

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
PROJECT_DIR="$(dirname "$PROJECT_DIR")"  # Go up from scripts/deployment

cd "$PROJECT_DIR" || exit 1

echo "🚀 Starting staging deployment..."
echo "   Time: $(date)"
echo "   Directory: $PROJECT_DIR"

# Load environment variables if .env exists
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
fi

# Set defaults
DB_CONTAINER="${DB_CONTAINER:-inventory-jovanni-db}"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.yml}"

# Step 1: Backup database
echo ""
echo "📦 Step 1: Database Backup"
if [ -f "$SCRIPT_DIR/backup-database.sh" ]; then
    bash "$SCRIPT_DIR/backup-database.sh" ./backups
else
    echo "⚠️  Backup script not found, skipping..."
fi

# Step 2: Pull latest code (if using git)
echo ""
echo "📥 Step 2: Pulling latest code..."
if [ -d .git ]; then
    git fetch origin staging || echo "⚠️  Git fetch failed, continuing..."
    git checkout staging || echo "⚠️  Git checkout failed, continuing..."
    git pull origin staging || echo "⚠️  Git pull failed, continuing..."
else
    echo "ℹ️  Not a git repository, skipping git pull"
fi

# Step 3: Install PHP dependencies inside the app service (uses image extensions)
echo ""
echo "🧩 Step 3: Installing PHP dependencies via app container (Composer)..."
docker compose -f "$COMPOSE_FILE" run --rm --entrypoint "" app \
  bash -lc "git config --global --add safe.directory /var/www && \
            composer install --no-dev --optimize-autoloader" || {
    echo "❌ Composer install failed inside app container. Aborting deployment."
    exit 1
  }

# Step 4: Build frontend assets inside the app service (Vite/Vite+Laravel)
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

# Step 6: Pull latest images (if using registry)
echo ""
echo "📥 Step 6: Pulling latest images..."
# Uncomment if using container registry:
# docker pull ghcr.io/dgreatnoob/inventory_jovanni:staging-latest || echo "⚠️  Image pull failed, will build locally"

# Step 7: Build and start containers
echo ""
echo "🔨 Step 7: Building and starting containers..."
RUN_MIGRATIONS=true docker compose -f "$COMPOSE_FILE" up -d --build

# Step 8: Wait for services to be ready
echo ""
echo "⏳ Step 8: Waiting for services to be ready..."
sleep 15

# Step 9: Run migrations (if not done by entrypoint)
echo ""
echo "📊 Step 9: Running database migrations..."
if docker ps | grep -q "inventory-jovanni-app"; then
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force || echo "⚠️  Migrations failed"
else
    echo "⚠️  App container not running, skipping migrations"
fi

# Step 10: Clear and cache config
echo ""
echo "⚡ Step 10: Optimizing application..."
if docker ps | grep -q "inventory-jovanni-app"; then
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:clear || true
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:cache || true
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan route:cache || true
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan view:cache || true
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan storage:link || true
else
    echo "⚠️  App container not running, skipping optimization"
fi

# Step 11: Health check
echo ""
echo "🏥 Step 11: Health check..."
sleep 5

if curl -f http://localhost/health 2>/dev/null || curl -f http://localhost 2>/dev/null; then
    echo "✅ Health check passed!"
else
    echo "⚠️  Health check failed, but deployment completed"
    echo "   Please verify manually: http://localhost"
fi

# Step 12: Show status
echo ""
echo "📋 Step 12: Container status..."
docker compose -f "$COMPOSE_FILE" ps

echo ""
echo "✅ Deployment completed!"
echo "   Time: $(date)"
echo "   Access: http://localhost"
echo ""
echo "📝 To view logs: docker compose -f $COMPOSE_FILE logs -f"
echo "📝 To restart: docker compose -f $COMPOSE_FILE restart"
