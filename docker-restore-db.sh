#!/bin/bash

# Docker Database Restore Script
# This script restores a database backup

set -e

echo "🔄 Database Restore Script"
echo "=========================="
echo ""

# Check if backup file is provided
if [ -z "$1" ]; then
    echo "❌ Error: No backup file specified"
    echo ""
    echo "Usage: $0 <backup_file.sql.gz> [--prod|--dev]"
    echo ""
    echo "Examples:"
    echo "  $0 backups/prod/db_backup_20250126_120000.sql.gz"
    echo "  $0 backups/prod/db_backup_20250126_120000.sql.gz --prod"
    echo "  $0 backups/dev/db_backup_20250126_120000.sql.gz --dev"
    exit 1
fi

BACKUP_FILE="$1"

# Check if backup file exists
if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Error: Backup file not found: $BACKUP_FILE"
    exit 1
fi

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
if [ "$2" = "--dev" ] || [ "$2" = "--development" ]; then
    COMPOSE_FILE="docker-compose.yml"
elif [ "$2" = "--prod" ] || [ "$2" = "--production" ]; then
    COMPOSE_FILE="docker-compose.prod.yml"
fi

# Get database configuration from environment or defaults
DB_NAME="${DB_DATABASE:-inventory_jovanni}"
DB_USER="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-rootsecret}"
DB_HOST="db"

echo "📦 Using compose file: $COMPOSE_FILE"
echo "💾 Database: $DB_NAME"
echo "📁 Backup file: $BACKUP_FILE"
echo ""

# Check if database container is running
if ! $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE ps db | grep -q "Up"; then
    echo "❌ Database container is not running!"
    echo "   Please start the containers first: $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE up -d"
    exit 1
fi

# Warning confirmation
echo "⚠️  WARNING: This will replace the current database!"
echo "   Database: $DB_NAME"
echo "   Backup file: $BACKUP_FILE"
echo ""
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo "❌ Restore cancelled."
    exit 0
fi

echo ""
echo "🔄 Restoring database backup..."

# Check if file is compressed
if [[ "$BACKUP_FILE" == *.gz ]]; then
    echo "   📦 Decompressing backup..."
    TEMP_FILE=$(mktemp)
    gunzip -c "$BACKUP_FILE" > "$TEMP_FILE"
    BACKUP_TO_RESTORE="$TEMP_FILE"
else
    BACKUP_TO_RESTORE="$BACKUP_FILE"
fi

# Restore the database
if $DOCKER_COMPOSE_CMD -f $COMPOSE_FILE exec -T db mysql \
    -u"$DB_USER" \
    -p"$DB_PASSWORD" \
    "$DB_NAME" < "$BACKUP_TO_RESTORE" 2>/dev/null; then
    echo "   ✅ Database restored successfully"
    
    # Clean up temp file if it was compressed
    if [[ "$BACKUP_FILE" == *.gz ]]; then
        rm -f "$TEMP_FILE"
    fi
    
    echo ""
    echo "======================================"
    echo "✅ Restore completed successfully!"
    echo ""
    echo "Database: $DB_NAME"
    echo "Restored from: $BACKUP_FILE"
    echo ""
    echo "You may need to clear Laravel caches:"
    echo "  ./docker-update-code.sh --prod"
    echo "======================================"
else
    echo "❌ Restore failed!"
    echo "   Please check database credentials and backup file integrity."
    
    # Clean up temp file if it was compressed
    if [[ "$BACKUP_FILE" == *.gz ]] && [ -n "$TEMP_FILE" ]; then
        rm -f "$TEMP_FILE"
    fi
    
    exit 1
fi
