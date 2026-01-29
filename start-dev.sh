#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}🚀 Starting Inventory Jovanni Development Environment${NC}"
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker is not running. Please start Docker and try again.${NC}"
    exit 1
fi

# Check if Docker Compose is installed and determine command
# Prefer docker compose (v2) - faster and more modern
# Fallback to docker-compose (v1) if v2 not available
DOCKER_COMPOSE_CMD=""

# Try docker compose (v2) first - preferred, faster
if docker compose version &> /dev/null 2>&1; then
    DOCKER_COMPOSE_CMD="docker compose"
fi

# If docker compose (v2) not available, try docker-compose (v1) as fallback
if [ -z "$DOCKER_COMPOSE_CMD" ]; then
    if command -v docker-compose &> /dev/null; then
        if docker-compose version &> /dev/null 2>&1; then
            DOCKER_COMPOSE_CMD="docker-compose"
        fi
    fi
fi

if [ -z "$DOCKER_COMPOSE_CMD" ]; then
    echo -e "${RED}❌ Docker Compose is not installed or not working.${NC}"
    echo -e "${YELLOW}   Docker Compose v2 should be included with Docker Desktop.${NC}"
    echo -e "${YELLOW}   If using WSL2, ensure Docker Desktop WSL integration is enabled:${NC}"
    echo -e "${YELLOW}   https://docs.docker.com/go/wsl2/${NC}"
    echo -e "${YELLOW}   ${NC}"
    echo -e "${YELLOW}   Alternative: Install docker-compose v1:${NC}"
    echo -e "${YELLOW}   sudo apt-get update && sudo apt-get install -y docker-compose${NC}"
    exit 1
fi

echo -e "${YELLOW}📦 Starting Docker containers (MySQL, Redis, phpMyAdmin)...${NC}"
echo -e "${YELLOW}   Using: $DOCKER_COMPOSE_CMD${NC}"

# Only start db, redis, and phpmyadmin for dev (skip app and nginx)
SERVICES="db redis phpmyadmin"

# Try to start containers
# Use timeout to prevent hanging (30 seconds max)
# Disable buildx to avoid plugin issues
if [[ "$DOCKER_COMPOSE_CMD" == "docker compose" ]]; then
    # docker compose (v2) - disable buildx to avoid plugin errors
    echo -e "${YELLOW}   Starting containers with docker compose (v2)...${NC}"
    export DOCKER_BUILDKIT=0
    export COMPOSE_DOCKER_CLI_BUILD=0
    if timeout 30 bash -c "$DOCKER_COMPOSE_CMD up -d $SERVICES" 2>&1; then
        COMPOSE_EXIT=0
    else
        COMPOSE_EXIT=$?
        if [ $COMPOSE_EXIT -eq 124 ]; then
            echo -e "${RED}❌ Docker compose command timed out after 30 seconds${NC}"
            echo -e "${YELLOW}   Check Docker daemon: docker info${NC}"
        fi
    fi
    unset DOCKER_BUILDKIT COMPOSE_DOCKER_CLI_BUILD
else
    # docker-compose (v1) fallback
    echo -e "${YELLOW}   Starting containers with docker-compose (v1)...${NC}"
    if timeout 30 bash -c "$DOCKER_COMPOSE_CMD up -d $SERVICES" 2>&1; then
        COMPOSE_EXIT=0
    else
        COMPOSE_EXIT=$?
        if [ $COMPOSE_EXIT -eq 124 ]; then
            echo -e "${RED}❌ Docker compose command timed out after 30 seconds${NC}"
            echo -e "${YELLOW}   Check Docker daemon: docker info${NC}"
        fi
    fi
fi

if [ $COMPOSE_EXIT -ne 0 ]; then
    echo -e "${RED}❌ Failed to start containers.${NC}"
    echo -e "${YELLOW}   Check Docker status: docker info${NC}"
    echo -e "${YELLOW}   Check container logs: $DOCKER_COMPOSE_CMD logs${NC}"
    echo -e "${YELLOW}   ${NC}"
    
    # If using docker compose (v2) failed, try docker-compose (v1) as fallback
    if [[ "$DOCKER_COMPOSE_CMD" == "docker compose" ]] && command -v docker-compose &> /dev/null; then
        echo -e "${YELLOW}   Trying docker-compose (v1) as fallback...${NC}"
        if timeout 30 docker-compose up -d 2>&1; then
            DOCKER_COMPOSE_CMD="docker-compose"
            echo -e "${GREEN}✅ Containers started using docker-compose (v1)${NC}"
        else
            echo -e "${RED}❌ Both docker compose versions failed.${NC}"
            exit 1
        fi
    else
        exit 1
    fi
else
    echo -e "${GREEN}✅ Containers started${NC}"
    
    # Verify containers are actually running
    echo -e "${YELLOW}🔍 Verifying containers are running...${NC}"
    sleep 3  # Give containers a moment to start
    
    if ! docker ps --format "{{.Names}}" | grep -q "inventory-jovanni-db"; then
        echo -e "${RED}❌ Database container failed to start${NC}"
        echo -e "${YELLOW}   Check logs: docker logs inventory-jovanni-db${NC}"
        echo -e "${YELLOW}   Or: $DOCKER_COMPOSE_CMD logs db${NC}"
        exit 1
    fi
    
    if ! docker ps --format "{{.Names}}" | grep -q "inventory-jovanni-redis"; then
        echo -e "${RED}❌ Redis container failed to start${NC}"
        echo -e "${YELLOW}   Check logs: docker logs inventory-jovanni-redis${NC}"
        echo -e "${YELLOW}   Or: $DOCKER_COMPOSE_CMD logs redis${NC}"
        exit 1
    fi
fi

# Wait for MySQL to be ready (with timeout)
echo -e "${YELLOW}⏳ Waiting for MySQL to be ready...${NC}"
MAX_WAIT=60
WAIT_COUNT=0
while [ $WAIT_COUNT -lt $MAX_WAIT ]; do
    # Check if container exists and is running
    if docker ps --format "{{.Names}}" | grep -q "^inventory-jovanni-db$"; then
        if docker exec inventory-jovanni-db mysqladmin ping -h localhost -u root -prootsecret --silent > /dev/null 2>&1; then
            echo ""
            echo -e "${GREEN}✅ MySQL is ready${NC}"
            break
        fi
    else
        echo -e "${YELLOW}   Container not found yet...${NC}"
    fi
    echo -n "."
    sleep 1
    WAIT_COUNT=$((WAIT_COUNT + 1))
done

if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
    echo ""
    echo -e "${RED}❌ MySQL did not become ready within ${MAX_WAIT} seconds${NC}"
    echo -e "${YELLOW}   Check container status: docker ps -a | grep inventory-jovanni-db${NC}"
    echo -e "${YELLOW}   Check logs: docker logs inventory-jovanni-db${NC}"
    exit 1
fi

# Wait for Redis to be ready (with timeout)
echo -e "${YELLOW}⏳ Waiting for Redis to be ready...${NC}"
MAX_WAIT=30
WAIT_COUNT=0
while [ $WAIT_COUNT -lt $MAX_WAIT ]; do
    # Check if container exists and is running
    if docker ps --format "{{.Names}}" | grep -q "^inventory-jovanni-redis$"; then
        if docker exec inventory-jovanni-redis redis-cli ping > /dev/null 2>&1; then
            echo ""
            echo -e "${GREEN}✅ Redis is ready${NC}"
            break
        fi
    else
        echo -e "${YELLOW}   Container not found yet...${NC}"
    fi
    echo -n "."
    sleep 1
    WAIT_COUNT=$((WAIT_COUNT + 1))
done

if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
    echo ""
    echo -e "${RED}❌ Redis did not become ready within ${MAX_WAIT} seconds${NC}"
    echo -e "${YELLOW}   Check container status: docker ps -a | grep inventory-jovanni-redis${NC}"
    echo -e "${YELLOW}   Check logs: docker logs inventory-jovanni-redis${NC}"
    exit 1
fi

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}❌ .env file not found. Please create it first.${NC}"
    exit 1
fi

# Fix permissions for Laravel
echo -e "${YELLOW}🔐 Fixing permissions...${NC}"
# Create directories if they don't exist
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set proper permissions (use current user, not www-data for local dev)
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
# Also ensure directories are writable by current user
chmod -R u+w storage bootstrap/cache 2>/dev/null || true

echo -e "${GREEN}✅ Permissions fixed${NC}"

# Install/update dependencies if needed
echo -e "${YELLOW}📥 Checking dependencies...${NC}"
if [ ! -d "vendor" ]; then
    echo -e "${YELLOW}Installing Composer dependencies...${NC}"
    if ! timeout 300 composer install; then
        echo -e "${RED}❌ Composer install timed out or failed${NC}"
        echo -e "${YELLOW}   You can continue without vendor/ or install manually later${NC}"
    fi
fi

if [ ! -d "node_modules" ]; then
    echo -e "${YELLOW}Installing npm dependencies...${NC}"
    if ! timeout 300 npm install; then
        echo -e "${RED}❌ npm install timed out or failed${NC}"
        echo -e "${YELLOW}   You can continue without node_modules/ or install manually later${NC}"
    fi
fi

# Run migrations
echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
if php artisan migrate --force 2>&1; then
    echo -e "${GREEN}✅ Migrations completed${NC}"
else
    echo -e "${YELLOW}⚠️  Migration failed or database not ready. You can run migrations manually later.${NC}"
    echo -e "${YELLOW}   Run: php artisan migrate${NC}"
fi

# Clear and cache config
echo -e "${YELLOW}🔧 Optimizing Laravel...${NC}"
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
echo -e "${GREEN}✅ Laravel optimized${NC}"

# Get local IP address for network access
LOCAL_IP=$(hostname -I 2>/dev/null | awk '{print $1}' || ip -4 addr show scope global | grep inet | awk '{print $2}' | cut -d/ -f1 | head -1)

# Display service information
echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ Development Environment Started Successfully!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo ""
if [ ! -z "$LOCAL_IP" ]; then
    echo -e "${GREEN}🌐 Access from Windows Browser (WSL2):${NC}"
    echo -e "   • Laravel App:    ${GREEN}http://${LOCAL_IP}:8000${NC}"
    echo -e "   • phpMyAdmin:     ${GREEN}http://${LOCAL_IP}:8081${NC}"
    echo ""
    echo -e "${YELLOW}💡 Tip: If the IP above doesn't work, try localhost:8000${NC}"
    echo ""
fi
echo -e "${GREEN}📍 Service URLs (WSL/Linux):${NC}"
echo -e "   • Laravel App:    ${GREEN}http://localhost:8000${NC}"
echo -e "   • phpMyAdmin:     ${GREEN}http://localhost:8081${NC}"
echo ""
echo -e "${GREEN}🗄️  Database Info:${NC}"
echo -e "   • MySQL:          ${GREEN}localhost:3307${NC}"
echo -e "   • Redis:          ${GREEN}localhost:6380${NC}"
echo ""
echo ""
echo -e "${YELLOW}💡 Starting Laravel development server...${NC}"
echo ""
if [ ! -z "$LOCAL_IP" ]; then
    echo -e "${YELLOW}📌 To access from Windows browser, run port forwarding:${NC}"
    echo -e "   ${YELLOW}PowerShell (Admin): .\\wsl-port-forward.ps1${NC}"
    echo -e "   ${YELLOW}Or use WSL IP directly: http://${LOCAL_IP}:8000${NC}"
    echo ""
fi
echo -e "${YELLOW}Press Ctrl+C to stop${NC}"
echo ""

# Check if port 8000 is already in use and stop existing server
if lsof -i :8000 >/dev/null 2>&1 || netstat -tuln 2>/dev/null | grep -q ":8000 " || ss -tuln 2>/dev/null | grep -q ":8000 "; then
    echo -e "${YELLOW}⚠️  Port 8000 is already in use${NC}"
    echo -e "${YELLOW}   Stopping any existing Laravel server...${NC}"
    pkill -f "php artisan serve" 2>/dev/null || true
    sleep 2
    
    # Double-check port is free now
    if lsof -i :8000 >/dev/null 2>&1 || netstat -tuln 2>/dev/null | grep -q ":8000 " || ss -tuln 2>/dev/null | grep -q ":8000 "; then
        echo -e "${RED}❌ Could not free port 8000. Please stop the process manually.${NC}"
        echo -e "${YELLOW}   Run: pkill -f 'php artisan serve'${NC}"
        exit 1
    fi
fi

# Start Laravel development server in background so script can exit
echo -e "${GREEN}🚀 Starting Laravel development server in background...${NC}"
php artisan serve --host=0.0.0.0 --port=8000 >/dev/null 2>&1 &
SERVER_PID=$!
echo -e "${YELLOW}   Laravel server PID: ${SERVER_PID}${NC}"
echo -e "${YELLOW}   To stop it later, run: kill ${SERVER_PID} or pkill -f 'php artisan serve'${NC}"

echo ""
echo -e "${GREEN}✅ Dev environment is ready. You can close this terminal or keep it open for logs.${NC}"
