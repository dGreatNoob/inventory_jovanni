#!/bin/bash

# Production Deployment Script for Inventory Jovanni
# This script handles the complete production deployment including image uploads

set -e

echo "🚀 Starting Production Deployment..."

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

# Database backup function
backup_database() {
    local backup_dir="backups"
    local timestamp=$(date +"%Y%m%d_%H%M%S")
    local backup_file="${backup_dir}/inventory_jovanni_backup_${timestamp}.sql"
    
    print_check "Creating database backup..."
    
    # Create backup directory
    mkdir -p "$backup_dir"
    
    # Check if database container is running
    if ! docker-compose -f docker-compose.prod.yml ps | grep -q "db.*Up"; then
        print_warning "Database container is not running. Skipping backup."
        return 0
    fi
    
    # Create database backup
    if docker-compose -f docker-compose.prod.yml exec -T db mysqldump \
        -u root -prootsecret \
        --single-transaction \
        --routines \
        --triggers \
        --add-drop-database \
        --databases inventory_jovanni > "$backup_file" 2>/dev/null; then
        
        # Compress backup
        if command -v gzip &> /dev/null; then
            gzip "$backup_file"
            backup_file="${backup_file}.gz"
            print_status "✅ Database backup created and compressed: $backup_file"
        else
            print_status "✅ Database backup created: $backup_file"
        fi
        
        # Verify backup file
        if [ -f "$backup_file" ] && [ -s "$backup_file" ]; then
            local backup_size=$(du -h "$backup_file" | cut -f1)
            print_status "✅ Backup verified: $backup_size"
            
            # Keep only last 5 backups
            find "$backup_dir" -name "inventory_jovanni_backup_*.sql*" -type f -printf '%T@ %p\n' | \
                sort -rn | tail -n +6 | cut -d' ' -f2- | xargs -r rm -f
            print_status "✅ Old backups cleaned up (keeping last 5)"
        else
            print_error "❌ Backup file is empty or missing"
            return 1
        fi
    else
        print_error "❌ Failed to create database backup"
        return 1
    fi
}

# Cleanup function for error handling
cleanup() {
    print_warning "Deployment failed. Cleaning up..."
    
    # Stop containers
    docker-compose -f docker-compose.prod.yml down 2>/dev/null || true
    
    # Remove test files
    rm -f storage/app/public/photos/test.txt 2>/dev/null || true
    
    print_error "Deployment cleanup completed."
    exit 1
}

# Handle command line arguments
if [ "$1" = "--backup-only" ]; then
    print_status "Running backup-only mode..."
    backup_database
    exit 0
fi

if [ "$1" = "--restore" ]; then
    if [ -z "$2" ]; then
        print_error "Please specify backup file: --restore <backup_file>"
        exit 1
    fi
    print_status "Restore mode not implemented yet. Please restore manually."
    exit 1
fi

# Set trap for cleanup on error
trap cleanup EXIT

# Pre-deployment checks
print_check "Running pre-deployment checks..."

# Check if running as root or with sudo
if [ "$EUID" -eq 0 ]; then
    print_warning "Running as root. This is not recommended for production."
    read -p "Continue anyway? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_error "Deployment cancelled."
        exit 1
    fi
fi

# Check if Docker is running
print_check "Checking Docker status..."
if ! docker info > /dev/null 2>&1; then
    print_error "Docker is not running. Please start Docker and try again."
    exit 1
fi
print_status "✅ Docker is running"

# Check if docker-compose is available
print_check "Checking Docker Compose availability..."
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed or not in PATH."
    exit 1
fi
print_status "✅ Docker Compose is available"

# Check if docker-compose.prod.yml exists
print_check "Checking production configuration..."
if [ ! -f "docker-compose.prod.yml" ]; then
    print_error "docker-compose.prod.yml not found!"
    exit 1
fi
print_status "✅ Production configuration found"

# Check if nginx production config exists
if [ ! -f "nginx/conf.d/production.conf" ]; then
    print_error "nginx/conf.d/production.conf not found!"
    exit 1
fi
print_status "✅ Nginx production configuration found"

# Check if Dockerfile exists
if [ ! -f "Dockerfile" ]; then
    print_error "Dockerfile not found!"
    exit 1
fi
print_status "✅ Dockerfile found"

# Check available disk space (minimum 2GB)
print_check "Checking available disk space..."
AVAILABLE_SPACE=$(df . | tail -1 | awk '{print $4}')
REQUIRED_SPACE=2097152  # 2GB in KB
if [ "$AVAILABLE_SPACE" -lt "$REQUIRED_SPACE" ]; then
    print_warning "Low disk space detected. Available: $(($AVAILABLE_SPACE/1024))MB, Recommended: 2GB+"
    read -p "Continue anyway? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_error "Deployment cancelled due to insufficient disk space."
        exit 1
    fi
else
    print_status "✅ Sufficient disk space available: $(($AVAILABLE_SPACE/1024))MB"
fi

# Check if ports are available
print_check "Checking port availability..."
if netstat -tuln 2>/dev/null | grep -q ":80 "; then
    print_warning "Port 80 is already in use. This may cause conflicts."
    read -p "Continue anyway? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_error "Deployment cancelled due to port conflict."
        exit 1
    fi
else
    print_status "✅ Port 80 is available"
fi

if netstat -tuln 2>/dev/null | grep -q ":8080 "; then
    print_warning "Port 8080 is already in use. This may cause conflicts."
    read -p "Continue anyway? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_error "Deployment cancelled due to port conflict."
        exit 1
    fi
else
    print_status "✅ Port 8080 is available"
fi

# Create database backup before deployment
print_status "Creating database backup before deployment..."
backup_database || {
    print_warning "Database backup failed, but continuing with deployment..."
    read -p "Continue without backup? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_error "Deployment cancelled due to backup failure."
        exit 1
    fi
}

# Stop any existing containers
print_status "Stopping existing containers..."
docker-compose -f docker-compose.prod.yml down 2>/dev/null || true

# Build the application image
print_status "Building application image..."
docker-compose -f docker-compose.prod.yml build --no-cache app

# Create necessary directories for image uploads
print_status "Creating directories for image uploads..."
mkdir -p storage/app/public/photos
mkdir -p storage/app/public/thumbnails
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Verify directory creation
print_check "Verifying directory creation..."
for dir in "storage/app/public/photos" "storage/app/public/thumbnails" "storage/logs" "bootstrap/cache"; do
    if [ -d "$dir" ]; then
        print_status "✅ Directory created: $dir"
    else
        print_error "❌ Failed to create directory: $dir"
        exit 1
    fi
done

# Set proper permissions for storage directories
print_status "Setting permissions for storage directories..."

# Function to set permissions with validation
set_permissions() {
    local path=$1
    local permissions=$2
    local owner=$3
    
    print_check "Setting permissions for $path..."
    
    if [ -n "$owner" ]; then
        if sudo chown -R "$owner" "$path" 2>/dev/null; then
            print_status "✅ Owner set for $path"
        else
            print_warning "⚠️  Could not set owner for $path (may need sudo)"
        fi
    fi
    
    if chmod -R "$permissions" "$path" 2>/dev/null; then
        print_status "✅ Permissions set for $path"
    else
        print_error "❌ Failed to set permissions for $path"
        return 1
    fi
    
    # Verify permissions
    local actual_perms=$(stat -c "%a" "$path" 2>/dev/null || echo "unknown")
    print_status "Verified permissions for $path: $actual_perms"
}

# Set permissions for storage directories
set_permissions "storage/" "775" "www-data:www-data" || {
    print_warning "Setting permissions with current user..."
    set_permissions "storage/" "775" ""
}

set_permissions "bootstrap/cache/" "775" "www-data:www-data" || {
    print_warning "Setting permissions with current user..."
    set_permissions "bootstrap/cache/" "775" ""
}

# Verify critical permissions
print_check "Verifying critical permissions..."
for path in "storage/app/public/photos" "storage/logs" "bootstrap/cache"; do
    if [ -w "$path" ]; then
        print_status "✅ Write permission verified: $path"
    else
        print_error "❌ No write permission: $path"
        exit 1
    fi
done

# Start the services
print_status "Starting production services..."
docker-compose -f docker-compose.prod.yml up -d

# Wait for services to be ready
print_status "Waiting for services to be ready..."
sleep 30

# Check if services are running
print_status "Checking service health..."

# Check database
if docker-compose -f docker-compose.prod.yml exec -T db mysqladmin ping -h localhost -u root -prootsecret > /dev/null 2>&1; then
    print_status "✅ Database is ready"
else
    print_error "❌ Database is not ready"
    exit 1
fi

# Check Redis
if docker-compose -f docker-compose.prod.yml exec -T redis redis-cli ping > /dev/null 2>&1; then
    print_status "✅ Redis is ready"
else
    print_error "❌ Redis is not ready"
    exit 1
fi

# Check application
if docker-compose -f docker-compose.prod.yml exec -T app php artisan --version > /dev/null 2>&1; then
    print_status "✅ Application is ready"
else
    print_error "❌ Application is not ready"
    exit 1
fi

# Run additional setup commands
print_status "Running additional setup commands..."

# Create storage link
docker-compose -f docker-compose.prod.yml exec -T app php artisan storage:link

# Clear and cache configuration
docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# Set proper permissions for uploaded files
print_status "Setting permissions for uploaded files..."

# Function to check and set container permissions
set_container_permissions() {
    local container=$1
    local path=$2
    local permissions=$3
    local owner=$4
    
    print_check "Setting permissions in container $container for $path..."
    
    # Check if container is running
    if ! docker-compose -f docker-compose.prod.yml ps | grep -q "$container.*Up"; then
        print_error "❌ Container $container is not running"
        return 1
    fi
    
    # Set owner if specified
    if [ -n "$owner" ]; then
        if docker-compose -f docker-compose.prod.yml exec -T "$container" chown -R "$owner" "$path" 2>/dev/null; then
            print_status "✅ Owner set in container for $path"
        else
            print_warning "⚠️  Could not set owner in container for $path"
        fi
    fi
    
    # Set permissions
    if docker-compose -f docker-compose.prod.yml exec -T "$container" chmod -R "$permissions" "$path" 2>/dev/null; then
        print_status "✅ Permissions set in container for $path"
    else
        print_error "❌ Failed to set permissions in container for $path"
        return 1
    fi
    
    # Verify permissions
    local actual_perms=$(docker-compose -f docker-compose.prod.yml exec -T "$container" stat -c "%a" "$path" 2>/dev/null | tr -d '\r\n' || echo "unknown")
    print_status "Verified container permissions for $path: $actual_perms"
}

# Set permissions in the app container
set_container_permissions "app" "/var/www/storage" "775" "www-data:www-data" || {
    print_error "❌ Failed to set permissions in app container"
    exit 1
}

# Verify critical paths in container
print_check "Verifying critical paths in container..."
for path in "/var/www/storage/app/public/photos" "/var/www/storage/logs" "/var/www/bootstrap/cache"; do
    if docker-compose -f docker-compose.prod.yml exec -T app test -w "$path" 2>/dev/null; then
        print_status "✅ Write permission verified in container: $path"
    else
        print_error "❌ No write permission in container: $path"
        exit 1
    fi
done

# Test image upload functionality
print_status "Testing image upload functionality..."

# Test directory accessibility
if docker-compose -f docker-compose.prod.yml exec -T app test -d /var/www/storage/app/public/photos; then
    print_status "✅ Image upload directory is accessible"
else
    print_warning "⚠️  Image upload directory may not be properly configured"
fi

# Test file creation in upload directory
print_check "Testing file creation in upload directory..."
if docker-compose -f docker-compose.prod.yml exec -T app touch /var/www/storage/app/public/photos/test.txt 2>/dev/null; then
    print_status "✅ Can create files in upload directory"
    # Clean up test file
    docker-compose -f docker-compose.prod.yml exec -T app rm -f /var/www/storage/app/public/photos/test.txt 2>/dev/null
else
    print_error "❌ Cannot create files in upload directory"
    exit 1
fi

# Test storage link
print_check "Testing storage link..."
if docker-compose -f docker-compose.prod.yml exec -T app test -L /var/www/public/storage; then
    print_status "✅ Storage link is properly created"
else
    print_warning "⚠️  Storage link may not be properly created"
fi

# Test nginx configuration
print_check "Testing nginx configuration..."
if docker-compose -f docker-compose.prod.yml exec -T nginx nginx -t 2>/dev/null; then
    print_status "✅ Nginx configuration is valid"
else
    print_error "❌ Nginx configuration is invalid"
    exit 1
fi

# Test application health
print_check "Testing application health..."
if curl -s -f http://localhost/health > /dev/null 2>&1; then
    print_status "✅ Application health check passed"
else
    print_warning "⚠️  Application health check failed (may need more time to start)"
fi

# Final permission summary
print_check "Final permission summary..."
echo ""
echo "📁 Directory Permissions Summary:"
echo "   Host storage/: $(stat -c "%a %U:%G" storage/ 2>/dev/null || echo 'unknown')"
echo "   Host bootstrap/cache/: $(stat -c "%a %U:%G" bootstrap/cache/ 2>/dev/null || echo 'unknown')"
echo "   Container storage/: $(docker-compose -f docker-compose.prod.yml exec -T app stat -c "%a %U:%G" /var/www/storage 2>/dev/null | tr -d '\r\n' || echo 'unknown')"
echo ""

# Display service information
print_status "Production deployment completed successfully!"
echo ""
echo "🌐 Application URLs:"
echo "   Main Application: http://localhost"
echo "   Database Admin:   http://localhost:8080"
echo ""
echo "📁 Image Uploads:"
echo "   Storage Path:     /var/www/storage/app/public/photos"
echo "   Web Access:       http://localhost/storage/photos/"
echo ""
echo "🔧 Management Commands:"
echo "   View logs:        docker-compose -f docker-compose.prod.yml logs -f"
echo "   Stop services:    docker-compose -f docker-compose.prod.yml down"
echo "   Restart app:      docker-compose -f docker-compose.prod.yml restart app"
echo "   Access app shell: docker-compose -f docker-compose.prod.yml exec app bash"
echo ""
echo "💾 Database Backups:"
echo "   Backup location:  ./backups/"
echo "   Latest backup:    $(ls -t backups/inventory_jovanni_backup_*.sql* 2>/dev/null | head -1 || echo 'None')"
echo "   Backup count:     $(ls backups/inventory_jovanni_backup_*.sql* 2>/dev/null | wc -l || echo '0')"
echo "   Manual backup:    ./deploy-production.sh --backup-only"
echo "   Restore database: ./restore-database.sh <backup_file>"
echo ""

# Show running containers
print_status "Running containers:"
docker-compose -f docker-compose.prod.yml ps

# Create post-deployment backup
print_status "Creating post-deployment backup..."
backup_database || {
    print_warning "Post-deployment backup failed, but deployment was successful."
}

# Disable cleanup trap on successful deployment
trap - EXIT

print_status "🎉 Production deployment completed successfully!"