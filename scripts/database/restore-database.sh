#!/bin/bash

# Database Restore Script for Inventory Jovanni
# Usage: ./restore-database.sh <backup_file>

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_check() {
    echo -e "${BLUE}[CHECK]${NC} $1"
}

# Check if backup file is provided
if [ -z "$1" ]; then
    print_error "Please provide backup file path"
    echo "Usage: $0 <backup_file>"
    echo ""
    echo "Available backups:"
    ls -la backups/inventory_jovanni_backup_*.sql* 2>/dev/null || echo "No backups found"
    exit 1
fi

BACKUP_FILE="$1"

# Check if backup file exists
if [ ! -f "$BACKUP_FILE" ]; then
    print_error "Backup file not found: $BACKUP_FILE"
    exit 1
fi

print_status "Starting database restore from: $BACKUP_FILE"

# Check if Docker is running
print_check "Checking Docker status..."
if ! docker info > /dev/null 2>&1; then
    print_error "Docker is not running. Please start Docker and try again."
    exit 1
fi

# Check if database container is running
print_check "Checking database container..."
if ! docker-compose -f docker-compose.prod.yml ps | grep -q "db.*Up"; then
    print_error "Database container is not running. Please start the services first:"
    echo "  docker-compose -f docker-compose.prod.yml up -d db"
    exit 1
fi

# Create a backup before restore
print_check "Creating backup before restore..."
timestamp=$(date +"%Y%m%d_%H%M%S")
pre_restore_backup="backups/pre_restore_backup_${timestamp}.sql"

mkdir -p backups

if docker-compose -f docker-compose.prod.yml exec -T db mysqldump \
    -u root -prootsecret \
    --single-transaction \
    --routines \
    --triggers \
    --add-drop-database \
    --databases inventory_jovanni > "$pre_restore_backup" 2>/dev/null; then
    print_status "✅ Pre-restore backup created: $pre_restore_backup"
else
    print_warning "⚠️  Could not create pre-restore backup"
fi

# Confirm restore
print_warning "This will REPLACE the current database with the backup!"
print_warning "Current database will be backed up as: $pre_restore_backup"
echo ""
read -p "Are you sure you want to continue? (yes/NO): " -r
if [[ ! $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
    print_error "Restore cancelled."
    exit 1
fi

# Check if backup file is compressed
if [[ "$BACKUP_FILE" == *.gz ]]; then
    print_check "Decompressing backup file..."
    if command -v gunzip &> /dev/null; then
        gunzip -c "$BACKUP_FILE" | docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -prootsecret
    else
        print_error "gunzip not available. Please decompress the file manually."
        exit 1
    fi
else
    print_check "Restoring database..."
    docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -prootsecret < "$BACKUP_FILE"
fi

if [ $? -eq 0 ]; then
    print_status "✅ Database restored successfully!"
    
    # Verify restore
    print_check "Verifying restore..."
    table_count=$(docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -prootsecret -e "USE inventory_jovanni; SHOW TABLES;" 2>/dev/null | wc -l)
    if [ "$table_count" -gt 1 ]; then
        print_status "✅ Restore verified: $((table_count-1)) tables found"
    else
        print_warning "⚠️  Restore verification failed - no tables found"
    fi
    
    # Clear application cache
    print_check "Clearing application cache..."
    docker-compose -f docker-compose.prod.yml exec -T app php artisan cache:clear 2>/dev/null || true
    docker-compose -f docker-compose.prod.yml exec -T app php artisan config:clear 2>/dev/null || true
    docker-compose -f docker-compose.prod.yml exec -T app php artisan route:clear 2>/dev/null || true
    docker-compose -f docker-compose.prod.yml exec -T app php artisan view:clear 2>/dev/null || true
    
    print_status "🎉 Database restore completed successfully!"
    echo ""
    echo "📊 Restore Summary:"
    echo "   Restored from:    $BACKUP_FILE"
    echo "   Pre-restore backup: $pre_restore_backup"
    echo "   Tables restored:  $((table_count-1))"
    echo ""
    echo "🔧 Next steps:"
    echo "   1. Test the application: http://localhost"
    echo "   2. Verify data integrity"
    echo "   3. Check application logs if needed"
    
else
    print_error "❌ Database restore failed!"
    print_warning "You can restore from the pre-restore backup: $pre_restore_backup"
    exit 1
fi
