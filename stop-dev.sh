#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🛑 Stopping Inventory Jovanni Development Environment${NC}"
echo ""

# Stop Docker containers
echo -e "${YELLOW}📦 Stopping Docker containers...${NC}"
docker compose down

echo ""
echo -e "${GREEN}✅ Development environment stopped${NC}"
echo ""
echo -e "${GREEN}💡 To start again, run: ./start-dev.sh${NC}"

