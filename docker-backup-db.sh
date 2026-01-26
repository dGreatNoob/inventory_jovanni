#!/bin/bash

# Docker Database Backup Script
# This script creates a backup of the production database

set -e

echo "💾 Database Backup Script"
echo "=========================="
echo ""

# Check if Docker Compose is installed
if command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE_CMD="docker-compose"
elif docker compose version &> /dev/null; then
    DOCKER_COMPOSE_CMD="docker compose"
else
    echo "❌ Docker Compose is not installed."
    exit 1
fi

# Determine compose file from argument or default to production
COMPOSE_FILE="docker-compose.prod.yml"
BACKUP_DIR="./backups"
if [ "$1" = "--dev" ] || [ "$1" = "--development" ]; then
    COMPOSE_FILE="docker-compose.yml"
    BACKUP_DIR="./backups/dev"
elif [ "$1" = "--prod" ] || [ "$1" = "--production" ]; then
    COMPOSE_FILE="docker-compose.prod.yml"
    BACKUP_DIR="./backups/prod"
fi

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Get database configuration from environment or defaults
DB_NAME="${DB_DATABASE:-inventory_jovanni}"
DB_USER="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-rootsecret}"
DB_HOST="db"

# Generate timestamp for backup filename
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/db_backup_${TIMESTAMP}.sql"
BACKUP_FILE_COMPRESSED="$BACKUP_FILE.gz"

echo "📦 Using compose file: $COMPOSE_FILE"
echo "💾 Database: $DB_NAME"
echo "📁 Backup directory: $BACKUP_DIR"
echo ""

# Check if database container is running
if ! $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE ps db | grep -q "Up"; then
    echo "❌ Database container is not running!"
    echo "   Please start the containers first: $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE up -d"
    exit 1
fi

echo "🔄 Creating database backup..."
# Create database backup using mysqldump inside the container
if $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T db mysqldump \
    -u"$DB_USER" \
    -p"$DB_PASSWORD" \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null; then
    echo "   ✅ Backup created: $BACKUP_FILE"
    
    # Get file size
    FILE_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo "   📊 Backup size: $FILE_SIZE"
    
    # Compress the backup
    echo ""
    echo "🗜️  Compressing backup..."
    gzip -f "$BACKUP_FILE"
    COMPRESSED_SIZE=$(du -h "$BACKUP_FILE_COMPRESSED" | cut -f1)
    echo "   ✅ Compressed backup: $BACKUP_FILE_COMPRESSED"
    echo "   📊 Compressed size: $COMPRESSED_SIZE"
    
    # Clean up old backups (keep last 30 days)
    echo ""
    echo "🧹 Cleaning up old backups (keeping last 30 days)..."
    find "$BACKUP_DIR" -name "db_backup_*.sql.gz" -type f -mtime +30 -delete 2>/dev/null || true
    OLD_BACKUPS_COUNT=$(find "$BACKUP_DIR" -name "db_backup_*.sql.gz" -type f | wc -l)
    echo "   ✅ Old backups cleaned. Remaining backups: $OLD_BACKUPS_COUNT"
    
    echo ""
    echo "======================================"
    echo "✅ Backup completed successfully!"
    echo ""
    echo "Backup file: $BACKUP_FILE_COMPRESSED"
    echo "Size: $COMPRESSED_SIZE"
    echo ""
    echo "To restore this backup, use:"
    echo "  ./docker-restore-db.sh $BACKUP_FILE_COMPRESSED"
    echo "======================================"
else
    echo "❌ Backup failed!"
    echo "   Please check database credentials and container status."
    exit 1
fi
