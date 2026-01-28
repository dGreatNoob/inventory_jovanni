#!/bin/bash
set -e

# Database backup script for staging/production
# Usage: ./backup-database.sh [backup_directory]

BACKUP_DIR="${1:-./backups}"
DB_CONTAINER="${DB_CONTAINER:-inventory-jovanni-db}"
DB_NAME="${DB_NAME:-inventory_jovanni}"
DB_USER="${DB_USER:-root}"
DB_PASSWORD="${DB_PASSWORD:-rootsecret}"

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Generate backup filename with timestamp
BACKUP_FILE="$BACKUP_DIR/backup_$(date +%Y%m%d_%H%M%S).sql"

echo "💾 Creating database backup..."
echo "   Container: $DB_CONTAINER"
echo "   Database: $DB_NAME"
echo "   Backup file: $BACKUP_FILE"

# Check if container is running
if ! docker ps | grep -q "$DB_CONTAINER"; then
    echo "⚠️  Warning: Database container '$DB_CONTAINER' is not running"
    echo "   Skipping backup..."
    exit 0
fi

# Create backup
if docker exec "$DB_CONTAINER" mysqldump -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null; then
    # Compress backup
    gzip -f "$BACKUP_FILE"
    BACKUP_FILE="${BACKUP_FILE}.gz"
    
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo "✅ Backup created successfully!"
    echo "   File: $BACKUP_FILE"
    echo "   Size: $BACKUP_SIZE"
    
    # Keep only last 10 backups
    echo "🧹 Cleaning old backups (keeping last 10)..."
    ls -t "$BACKUP_DIR"/backup_*.sql.gz 2>/dev/null | tail -n +11 | xargs rm -f 2>/dev/null || true
    
    echo "✅ Backup complete!"
else
    echo "❌ Backup failed!"
    exit 1
fi
