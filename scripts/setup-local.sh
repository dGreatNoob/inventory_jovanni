#!/bin/bash

# Inventory Jovanni - Local Setup Script
# This script automates the local deployment process

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to check PHP version
check_php_version() {
    if command_exists php; then
        PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
        REQUIRED_VERSION="8.2"
        if [ "$(printf '%s\n' "$REQUIRED_VERSION" "$PHP_VERSION" | sort -V | head -n1)" = "$REQUIRED_VERSION" ]; then
            print_success "PHP version $PHP_VERSION is compatible"
            return 0
        else
            print_error "PHP version $PHP_VERSION is not compatible. Required: $REQUIRED_VERSION or higher"
            return 1
        fi
    else
        print_error "PHP is not installed"
        return 1
    fi
}

# Function to check required PHP extensions
check_php_extensions() {
    print_status "Checking PHP extensions..."
    
    REQUIRED_EXTENSIONS=("pdo_mysql" "mbstring" "openssl" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo" "gd" "zip" "curl")
    MISSING_EXTENSIONS=()
    
    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if ! php -m | grep -q "^$ext$"; then
            MISSING_EXTENSIONS+=("$ext")
        fi
    done
    
    if [ ${#MISSING_EXTENSIONS[@]} -eq 0 ]; then
        print_success "All required PHP extensions are installed"
        return 0
    else
        print_error "Missing PHP extensions: ${MISSING_EXTENSIONS[*]}"
        print_status "Install them with: sudo apt install php8.2-${MISSING_EXTENSIONS[*]// / php8.2-}"
        return 1
    fi
}

# Function to install Composer if not present
install_composer() {
    if command_exists composer; then
        print_success "Composer is already installed"
        return 0
    fi
    
    print_status "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    print_success "Composer installed successfully"
}

# Function to install Node.js if not present
install_nodejs() {
    if command_exists node && command_exists npm; then
        NODE_VERSION=$(node --version | cut -d'v' -f2 | cut -d'.' -f1)
        if [ "$NODE_VERSION" -ge 18 ]; then
            print_success "Node.js version $NODE_VERSION is compatible"
            return 0
        fi
    fi
    
    print_status "Installing Node.js..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo apt-get install -y nodejs
    print_success "Node.js installed successfully"
}

# Function to setup environment file
setup_environment() {
    if [ ! -f .env ]; then
        print_status "Creating .env file..."
        cp .env.example .env
        print_success ".env file created"
    else
        print_warning ".env file already exists, skipping..."
    fi
}

# Function to install PHP dependencies
install_php_dependencies() {
    print_status "Installing PHP dependencies..."
    
    # Increase memory limit for composer
    php -d memory_limit=2G /usr/local/bin/composer install --no-dev --optimize-autoloader --no-scripts
    
    # Run post-install scripts
    php artisan package:discover --ansi
    
    print_success "PHP dependencies installed"
}

# Function to install Node.js dependencies
install_node_dependencies() {
    print_status "Installing Node.js dependencies..."
    npm install
    print_success "Node.js dependencies installed"
}

# Function to build assets
build_assets() {
    print_status "Building frontend assets..."
    npm run build
    print_success "Frontend assets built"
}

# Function to generate application key
generate_app_key() {
    print_status "Generating application key..."
    php artisan key:generate --ansi
    print_success "Application key generated"
}

# Function to setup database
setup_database() {
    print_status "Setting up database..."
    
    # Check if MySQL is running
    if ! systemctl is-active --quiet mysql; then
        print_error "MySQL is not running. Please start MySQL first:"
        print_status "sudo systemctl start mysql"
        return 1
    fi
    
    # Create database and user
    print_status "Creating database and user..."
    mysql -u root -p << EOF
CREATE DATABASE IF NOT EXISTS inventory_jovanni;
CREATE USER IF NOT EXISTS 'jovanni'@'localhost' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON inventory_jovanni.* TO 'jovanni'@'localhost';
FLUSH PRIVILEGES;
EOF
    
    print_success "Database setup completed"
}

# Function to run migrations
run_migrations() {
    print_status "Running database migrations..."
    php artisan migrate --force
    print_success "Database migrations completed"
}

# Function to seed database
seed_database() {
    print_status "Seeding database..."
    php artisan db:seed --force
    print_success "Database seeded"
}

# Function to set permissions
set_permissions() {
    print_status "Setting proper permissions..."
    
    # Get the current directory
    CURRENT_DIR=$(pwd)
    
    # Set ownership
    sudo chown -R www-data:www-data "$CURRENT_DIR"
    
    # Set permissions
    sudo chmod -R 755 "$CURRENT_DIR"
    sudo chmod -R 775 "$CURRENT_DIR/storage"
    sudo chmod -R 775 "$CURRENT_DIR/bootstrap/cache"
    
    print_success "Permissions set"
}

# Function to optimize for production
optimize_production() {
    print_status "Optimizing for production..."
    
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    print_success "Application optimized for production"
}

# Main setup function
main() {
    echo "=========================================="
    echo "  Inventory Jovanni - Local Setup"
    echo "=========================================="
    echo ""
    
    # Check if running as root
    if [ "$EUID" -eq 0 ]; then
        print_error "Please do not run this script as root"
        exit 1
    fi
    
    # Check system requirements
    print_status "Checking system requirements..."
    
    if ! check_php_version; then
        print_error "Please install PHP 8.2 or higher"
        exit 1
    fi
    
    if ! check_php_extensions; then
        print_error "Please install missing PHP extensions"
        exit 1
    fi
    
    # Install dependencies
    install_composer
    install_nodejs
    
    # Setup application
    setup_environment
    install_php_dependencies
    install_node_dependencies
    build_assets
    generate_app_key
    
    # Database setup
    read -p "Do you want to setup the database? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        setup_database
        run_migrations
        
        read -p "Do you want to seed the database with sample data? (y/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            seed_database
        fi
    fi
    
    # Set permissions
    set_permissions
    
    # Optimize for production
    optimize_production
    
    echo ""
    echo "=========================================="
    print_success "Setup completed successfully!"
    echo "=========================================="
    echo ""
    echo "Next steps:"
    echo "1. Configure your web server (Nginx/Apache)"
    echo "2. Update .env file with your production settings"
    echo "3. Set up SSL certificate"
    echo "4. Configure Redis (optional but recommended)"
    echo ""
    echo "For detailed instructions, see LOCAL_DEPLOYMENT_GUIDE.md"
    echo ""
}

# Run main function
main "$@"
