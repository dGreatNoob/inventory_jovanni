#!/bin/bash

# Fix Migration Issues Script
# This script helps resolve migration conflicts after merging PRs

set -e  # Exit on error

echo "=========================================="
echo "  Fixing Migration Issues"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to check if PHP is available
check_php() {
    if command -v php >/dev/null 2>&1; then
        echo -e "${GREEN}✓${NC} PHP is available"
        return 0
    else
        echo -e "${RED}✗${NC} PHP is not available. Trying Docker..."
        return 1
    fi
}

# Function to run artisan command
run_artisan() {
    if check_php; then
        php artisan "$@"
    elif [ -f "/.dockerenv" ]; then
        # We're inside a Docker container
        php artisan "$@"
    elif command -v docker-compose >/dev/null 2>&1; then
        # Try to run via docker-compose
        docker-compose exec app php artisan "$@"
    elif command -v docker >/dev/null 2>&1; then
        # Try to run via docker
        docker exec inventory-jovanni-app php artisan "$@"
    else
        echo -e "${RED}Error: Cannot run artisan commands. Please install PHP or Docker.${NC}"
        exit 1
    fi
}

# Step 1: Clear caches
echo "Step 1: Clearing caches..."
run_artisan cache:clear 2>/dev/null || true
run_artisan config:clear 2>/dev/null || true
echo -e "${GREEN}✓${NC} Caches cleared"

# Step 2: Check migration status
echo ""
echo "Step 2: Checking migration status..."
echo "----------------------------------------"
run_artisan migrate:status || true

# Step 3: Try to run migrations with --pretend first
echo ""
echo "Step 3: Testing migrations (dry run)..."
echo "----------------------------------------"
run_artisan migrate --pretend || {
    echo -e "${YELLOW}⚠${NC} Migration dry run failed. Checking for issues..."
    
    # Check for specific issues
    echo ""
    echo "Checking for common issues:"
    
    # Check if suppliers table exists
    if run_artisan tinker --execute="echo Schema::hasTable('suppliers') ? 'true' : 'false';" 2>/dev/null | grep -q "true"; then
        echo -e "${GREEN}✓${NC} Suppliers table exists"
        
        # Check for duplicate columns
        echo "Checking suppliers table columns..."
        run_artisan tinker --execute="print_r(Schema::getColumnListing('suppliers'));" 2>/dev/null || true
    else
        echo -e "${YELLOW}⚠${NC} Suppliers table does not exist yet"
    fi
    
    # Check if agents table exists
    if run_artisan tinker --execute="echo Schema::hasTable('agents') ? 'true' : 'false';" 2>/dev/null | grep -q "true"; then
        echo -e "${GREEN}✓${NC} Agents table exists"
    else
        echo -e "${YELLOW}⚠${NC} Agents table does not exist yet"
    fi
    
    # Check if branches table exists
    if run_artisan tinker --execute="echo Schema::hasTable('branches') ? 'true' : 'false';" 2>/dev/null | grep -q "true"; then
        echo -e "${GREEN}✓${NC} Branches table exists"
    else
        echo -e "${YELLOW}⚠${NC} Branches table does not exist yet"
    fi
}

# Step 4: Ask user if they want to proceed with actual migration
echo ""
echo "=========================================="
echo -e "${YELLOW}Ready to run migrations.${NC}"
echo "This will modify your database."
echo ""
read -p "Do you want to proceed? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Running migrations..."
    run_artisan migrate --force || {
        echo -e "${RED}Migration failed!${NC}"
        echo ""
        echo "Possible solutions:"
        echo "1. Check the error message above"
        echo "2. If it's a duplicate column error, the migration file has been fixed"
        echo "3. If tables don't exist, make sure to run migrations in order"
        echo "4. You may need to rollback: php artisan migrate:rollback"
        echo "5. Or fresh migrate (WARNING: This will delete all data): php artisan migrate:fresh"
        exit 1
    }
    echo -e "${GREEN}✓${NC} Migrations completed successfully!"
else
    echo "Migration cancelled."
fi

echo ""
echo "=========================================="
echo "  Migration Fix Complete"
echo "=========================================="