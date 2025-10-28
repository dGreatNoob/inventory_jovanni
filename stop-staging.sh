#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🛑 Stopping Inventory Jovanni Staging Environment${NC}"
echo ""

# Stop Docker containers
echo -e "${YELLOW}📦 Stopping Docker containers...${NC}"
docker compose -f docker-compose.prod.yml down

echo ""
echo -e "${GREEN}✅ Staging environment stopped${NC}"
echo ""
echo -e "${GREEN}💡 To start again, run: ./start-staging.sh${NC}"

