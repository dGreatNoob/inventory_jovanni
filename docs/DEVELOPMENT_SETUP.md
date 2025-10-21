# 🚀 Jovanni Bags - Development Setup Guide

A comprehensive guide to set up and run the Jovanni Bags inventory management system for development.

## 📋 Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Detailed Setup](#detailed-setup)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
- [Application Setup](#application-setup)
- [Development Workflow](#development-workflow)
- [Troubleshooting](#troubleshooting)
- [Project Structure](#project-structure)

---

## 🔧 Prerequisites

Before starting, ensure you have the following installed:

- **Docker & Docker Compose** (latest version)
- **PHP 8.3+** (for local development)
- **Composer** (PHP dependency manager)
- **Node.js 18+** and **npm** (for frontend assets)
- **Git** (for version control)

### Installation Commands

**Ubuntu/Debian:**
```bash
# Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# PHP 8.3
sudo apt update
sudo apt install php8.3 php8.3-cli php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-gd

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

---

## ⚡ Quick Start

For experienced developers who want to get running quickly:

```bash
# 1. Clone the repository
git clone <repository-url>
cd inventory_jovanni

# 2. Setup environment
cp .env.example .env

# 3. Start database
docker compose up -d db phpmyadmin

# 4. Install dependencies
composer install
npm install

# 5. Generate application key
php artisan key:generate

# 6. Run migrations
php artisan migrate:fresh --seed

# 7. Build frontend assets
npm run build

# 8. Start development server
php artisan serve --host=0.0.0.0 --port=8000
```

**Access the application:**
- **Application**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8081

---

## 📝 Detailed Setup

### Step 1: Clone Repository

```bash
git clone <repository-url>
cd inventory_jovanni
```

### Step 2: Environment Configuration

Copy the example environment file and configure it:

```bash
cp .env.example .env
```

The `.env` file should contain the following key configurations:

```env
# ==============================================
# Laravel Environment Configuration
# ==============================================

APP_NAME="Jovanni Bags"
APP_ENV=local
APP_KEY=
APP_DEBUG=true

APP_URL=http://localhost:8000
VITE_DEV_SERVER_URL=http://localhost:5173

# ==============================================
# Database Configuration (Docker)
# ==============================================

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=inventory_jovanni
DB_USERNAME=jovanni
DB_PASSWORD=secret

# ==============================================
# Session Configuration
# ==============================================

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# ==============================================
# Broadcasting & Queue
# ==============================================

BROADCAST_CONNECTION=reverb
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

# ==============================================
# Cache Configuration
# ==============================================

CACHE_STORE=database
MEMCACHED_HOST=127.0.0.1

# ==============================================
# Redis Configuration (Optional)
# ==============================================

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# ==============================================
# Mail Configuration
# ==============================================

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# ==============================================
# Frontend Build Configuration
# ==============================================

VITE_APP_NAME="${APP_NAME}"

# ==============================================
# Reverb Configuration (Real-time)
# ==============================================

REVERB_APP_ID=116692
REVERB_APP_KEY=y6tzoywvblbve39gvsuh
REVERB_APP_SECRET=geerxqbgevoymkkzmx5f
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Step 3: Docker Services Setup

The application uses Docker for database and phpMyAdmin services:

```bash
# Start database and phpMyAdmin
docker compose up -d db phpmyadmin

# Check if services are running
docker compose ps
```

**Expected output:**
```
NAME                           IMAGE                          STATUS         PORTS
inventory-jovanni-db           mysql:8.0                      Up             33060/tcp, 0.0.0.0:3307->3306/tcp
inventory-jovanni-phpmyadmin   phpmyadmin/phpmyadmin:latest   Up             0.0.0.0:8081->80/tcp
```

### Step 4: Application Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 5: Laravel Application Setup

```bash
# Generate application key
php artisan key:generate

# Run database migrations and seed data
php artisan migrate:fresh --seed

# Clear and cache configuration
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 6: Frontend Assets

```bash
# Build production assets
npm run build

# OR for development with hot reloading
npm run dev
```

### Step 7: Start Development Server

```bash
# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000
```

---

## 🗄️ Database Setup

### Docker Database Configuration

The database is configured in `docker-compose.yml`:

```yaml
db:
  image: mysql:8.0
  container_name: inventory-jovanni-db
  restart: unless-stopped
  ports:
    - "3307:3306"
  environment:
    MYSQL_DATABASE: inventory_jovanni
    MYSQL_USER: jovanni
    MYSQL_PASSWORD: secret
    MYSQL_ROOT_PASSWORD: rootsecret
  volumes:
    - dbdata:/var/lib/mysql

phpmyadmin:
  image: phpmyadmin/phpmyadmin:latest
  container_name: inventory-jovanni-phpmyadmin
  restart: unless-stopped
  ports:
    - "8081:80"
  environment:
    PMA_HOST: db
    PMA_PORT: 3306
    PMA_USER: jovanni
    PMA_PASSWORD: secret
    MYSQL_ROOT_PASSWORD: rootsecret
  depends_on:
    - db
```

### Database Connection Details

| Setting | Value |
|---------|-------|
| **Host** | 127.0.0.1 (localhost) |
| **Port** | 3307 |
| **Database** | inventory_jovanni |
| **Username** | jovanni |
| **Password** | secret |
| **Root Password** | rootsecret |

### phpMyAdmin Access

- **URL**: http://localhost:8081
- **Username**: jovanni
- **Password**: secret
- **Auto-login**: Configured

---

## 🔄 Development Workflow

### Daily Development Commands

```bash
# Start your development session
docker compose up -d db phpmyadmin
php artisan serve --host=0.0.0.0 --port=8000

# In another terminal for frontend development
npm run dev
```

### Common Development Tasks

```bash
# Database operations
php artisan migrate                    # Run new migrations
php artisan migrate:fresh --seed      # Reset database with fresh data
php artisan db:seed                   # Seed database with test data

# Cache management
php artisan cache:clear              # Clear application cache
php artisan config:clear             # Clear configuration cache
php artisan route:clear              # Clear route cache
php artisan view:clear               # Clear view cache

# Frontend development
npm run dev                          # Start Vite dev server with hot reload
npm run build                        # Build production assets
npm run build --watch               # Build and watch for changes

# Testing
php artisan test                     # Run PHPUnit tests
```

### File Watching for Development

For optimal development experience, run these commands in separate terminals:

```bash
# Terminal 1: Backend server
php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2: Frontend assets with hot reload
npm run dev

# Terminal 3: Database services
docker compose up -d db phpmyadmin
```

---

## 🐛 Troubleshooting

### Common Issues and Solutions

#### 1. Database Connection Issues

**Problem**: Cannot connect to database
```bash
# Solution: Check if Docker containers are running
docker compose ps

# If not running, start them
docker compose up -d db phpmyadmin

# Check database connectivity
php artisan db:test
```

#### 2. Port Already in Use

**Problem**: Port 8000 or 3307 is already in use
```bash
# Kill processes using the ports
sudo lsof -ti:8000 | xargs kill -9
sudo lsof -ti:3307 | xargs kill -9

# Or use different ports
php artisan serve --host=0.0.0.0 --port=8001
```

#### 3. Permission Issues

**Problem**: Permission denied errors
```bash
# Fix Laravel storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### 4. Composer Dependencies

**Problem**: Composer install fails
```bash
# Clear composer cache and reinstall
composer clear-cache
composer install --ignore-platform-reqs
```

#### 5. Node.js Dependencies

**Problem**: npm install fails
```bash
# Clear npm cache and reinstall
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```

#### 6. Docker Issues

**Problem**: Docker containers won't start
```bash
# Clean up Docker resources
docker compose down --volumes --remove-orphans
docker system prune -f

# Recreate containers
docker compose up -d db phpmyadmin
```

### Environment Verification

Run this command to verify your setup:

```bash
# Check PHP version
php --version

# Check Composer
composer --version

# Check Node.js
node --version
npm --version

# Check Docker
docker --version
docker compose version

# Test database connection
php artisan db:test

# Check Laravel configuration
php artisan config:show
```

---

## 📁 Project Structure

```
inventory_jovanni/
├── app/                          # Laravel application code
│   ├── Livewire/                # Livewire components
│   ├── Models/                  # Eloquent models
│   └── ...
├── resources/
│   ├── views/                   # Blade templates
│   │   ├── components/          # Reusable components
│   │   ├── layouts/             # Layout templates
│   │   └── livewire/            # Livewire views
│   ├── css/                     # Stylesheets
│   └── js/                      # JavaScript files
├── public/
│   ├── images/                  # Static images
│   │   ├── jovanni_logo_black.png
│   │   └── jovanni_logo_white.png
│   └── build/                   # Compiled assets
├── database/
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Database seeders
├── docker-compose.yml           # Docker services configuration
├── .env.example                 # Environment template
├── package.json                 # Node.js dependencies
├── composer.json                # PHP dependencies
└── vite.config.js               # Vite build configuration
```

---

## 🎯 Key Features Implemented

### UI/UX Improvements
- ✅ **Dynamic Logo System**: Automatic switching between light/dark mode logos
- ✅ **Responsive Design**: Optimized for all screen sizes
- ✅ **Theme Support**: Dark and light mode compatibility
- ✅ **Brand Identity**: "Jovanni Bags" branding throughout

### Development Tools
- ✅ **Docker Database**: MySQL 8.0 with phpMyAdmin
- ✅ **Hot Reload**: Vite development server
- ✅ **Database Seeding**: Pre-populated test data
- ✅ **Environment Management**: Comprehensive .env configuration

### Performance Optimizations
- ✅ **Asset Compilation**: Optimized build process
- ✅ **Caching Strategy**: Multiple cache layers
- ✅ **Database Optimization**: Proper indexing and relationships

---

## 📞 Support

If you encounter any issues during setup:

1. **Check the troubleshooting section** above
2. **Verify all prerequisites** are installed correctly
3. **Ensure Docker services** are running properly
4. **Check environment variables** match the configuration
5. **Review Laravel logs** in `storage/logs/`

---

## 🔄 Updates and Maintenance

### Regular Maintenance Tasks

```bash
# Update dependencies
composer update
npm update

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild assets
npm run build
```

### Database Maintenance

```bash
# Backup database
docker exec inventory-jovanni-db mysqldump -u jovanni -psecret inventory_jovanni > backup.sql

# Restore database
docker exec -i inventory-jovanni-db mysql -u jovanni -psecret inventory_jovanni < backup.sql
```

---

**Happy coding! 🚀**

This setup provides a robust development environment for the Jovanni Bags inventory management system with all the optimizations and configurations we've implemented.
