#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}🚀 Starting Inventory Jovanni Staging Environment${NC}"
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker is not running. Please start Docker and try again.${NC}"
    exit 1
fi

# Create necessary directories
echo -e "${YELLOW}📁 Creating necessary directories...${NC}"
mkdir -p storage/app/public/photos
mkdir -p storage/app/public/thumbnails
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
echo -e "${YELLOW}🔐 Setting permissions...${NC}"
chmod -R 775 storage bootstrap/cache

echo -e "${YELLOW}📦 Starting Docker containers (Nginx, Laravel, MySQL, Redis, phpMyAdmin)...${NC}"
docker compose -f docker-compose.prod.yml up -d --build

# Wait for services to be ready
echo -e "${YELLOW}⏳ Waiting for services to be ready...${NC}"
sleep 10

# Wait for MySQL to be ready
while ! docker exec inventory-jovanni-db-prod mysqladmin ping -h localhost -u root -prootsecret --silent > /dev/null 2>&1; do
    echo -n "."
    sleep 1
done
echo ""
echo -e "${GREEN}✅ MySQL is ready${NC}"

# Wait for Redis to be ready
while ! docker exec inventory-jovanni-redis-prod redis-cli ping > /dev/null 2>&1; do
    echo -n "."
    sleep 1
done
echo ""
echo -e "${GREEN}✅ Redis is ready${NC}"

# Wait for Laravel app to be ready
echo -e "${YELLOW}⏳ Waiting for Laravel application...${NC}"
sleep 5

# Display service information
echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ Staging Environment Started Successfully!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${GREEN}📍 Service URLs:${NC}"
echo -e "   • Laravel App:    ${GREEN}http://localhost${NC}"
echo -e "   • phpMyAdmin:     ${GREEN}http://localhost:8081${NC}"
echo ""
echo -e "${GREEN}🗄️  Database Info:${NC}"
echo -e "   • MySQL:          ${GREEN}localhost:3306 (internal)${NC}"
echo -e "   • Redis:          ${GREEN}localhost:6379 (internal)${NC}"
echo ""
echo -e "${GREEN}🐳 Docker Containers:${NC}"
docker compose -f docker-compose.prod.yml ps

echo ""
echo -e "${YELLOW}💡 To stop the environment, run: ./stop-staging.sh${NC}"
echo -e "${YELLOW}📋 To view logs, run: docker compose -f docker-compose.prod.yml logs -f${NC}"
echo ""

