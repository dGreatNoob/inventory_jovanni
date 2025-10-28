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

echo -e "${YELLOW}📦 Starting Docker containers (MySQL, Redis, phpMyAdmin)...${NC}"
docker compose up -d

# Wait for MySQL to be ready
echo -e "${YELLOW}⏳ Waiting for MySQL to be ready...${NC}"
while ! docker exec inventory-jovanni-db mysqladmin ping -h localhost -u root -prootsecret --silent > /dev/null 2>&1; do
    echo -n "."
    sleep 1
done
echo ""
echo -e "${GREEN}✅ MySQL is ready${NC}"

# Wait for Redis to be ready
echo -e "${YELLOW}⏳ Waiting for Redis to be ready...${NC}"
while ! docker exec inventory-jovanni-redis redis-cli ping > /dev/null 2>&1; do
    echo -n "."
    sleep 1
done
echo ""
echo -e "${GREEN}✅ Redis is ready${NC}"

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}❌ .env file not found. Please create it first.${NC}"
    exit 1
fi

# Install/update dependencies if needed
echo -e "${YELLOW}📥 Checking dependencies...${NC}"
if [ ! -d "vendor" ]; then
    echo -e "${YELLOW}Installing Composer dependencies...${NC}"
    composer install
fi

if [ ! -d "node_modules" ]; then
    echo -e "${YELLOW}Installing npm dependencies...${NC}"
    npm install
fi

# Run migrations
echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
php artisan migrate --force

# Clear and cache config
echo -e "${YELLOW}🔧 Optimizing Laravel...${NC}"
php artisan config:clear
php artisan cache:clear

# Display service information
echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ Development Environment Started Successfully!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${GREEN}📍 Service URLs:${NC}"
echo -e "   • Laravel App:    ${GREEN}http://localhost:8000${NC}"
echo -e "   • phpMyAdmin:     ${GREEN}http://localhost:8081${NC}"
echo ""
echo -e "${GREEN}🗄️  Database Info:${NC}"
echo -e "   • MySQL:          ${GREEN}localhost:3307${NC}"
echo -e "   • Redis:          ${GREEN}localhost:6380${NC}"
echo ""
echo -e "${YELLOW}💡 Starting Laravel development server...${NC}"
echo -e "${YELLOW}Press Ctrl+C to stop${NC}"
echo ""

# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000

