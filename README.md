# 🎒 Jovanni Bags - Inventory Management System

[![Development Status](https://img.shields.io/badge/Status-Test_Production_Ready-orange.svg)](https://github.com/dGreatNoob/inventory_jovanni/tree/dev)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://docker.com)

> **🚀 Modern Laravel-based inventory management system for bag retail businesses**

## 🏢 About Jovanni Bags

Jovanni Bags is a **comprehensive inventory management system** built with Laravel and Livewire, designed specifically for bag retail businesses. This system provides real-time inventory tracking, customer management, supplier relationships, and comprehensive reporting capabilities.

### 🌟 Key Features

- **📦 Inventory Management** - Real-time stock tracking with automated alerts
- **👥 Customer Management** - Complete customer profiles and purchase history
- **🏪 Branch Management** - Multi-branch operations with centralized control
- **📊 Dashboard Analytics** - Real-time insights and performance metrics
- **🎨 Modern UI/UX** - Responsive design with dark/light mode support
- **🔄 Real-time Updates** - Live data synchronization across all modules
- **📱 Mobile Responsive** - Optimized for all device sizes
- **🔐 Role-based Access** - Secure multi-user system with permissions

## 🎯 Production Status

### ✅ Ready for Test Production
- **Product Management Module** - Complete with CRUD operations, categories, images, and inventory tracking
- **Dashboard** - Real-time statistics and product-focused analytics
- **Core Infrastructure** - Database, authentication, and basic UI components

### ⚠️ Under Revision
- Sales Management, Finance, Shipment Management, and other modules are currently under revision

**See [PRODUCTION_READINESS.md](./PRODUCTION_READINESS.md) for detailed status and deployment instructions.**

## 🚀 Quick Start

### Prerequisites

- **Docker & Docker Compose** (latest version)
- **PHP 8.3+** (for local development)
- **Composer** (PHP dependency manager)
- **Node.js 18+** and **npm** (for frontend assets)

### Local Development Setup

```bash
# 1. Clone the repository
git clone https://github.com/dGreatNoob/inventory_jovanni.git
cd inventory_jovanni

# 2. Setup environment
cp .env.example .env

# 3. Start database services
docker compose up -d db phpmyadmin

# 4. Install dependencies
composer install
npm install

# 5. Generate application key
php artisan key:generate

# 6. Run migrations and seed data
php artisan migrate:fresh --seed

# 7. Build frontend assets
npm run build

# 8. Start development server
php artisan serve --host=0.0.0.0 --port=8000
```

### Access Points

- **Application**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8081 (Database management)
- **Default Login**: Use seeded user credentials from database

## 🛠️ Tech Stack

| Component | Technology | Version |
|-----------|------------|---------|
| **Backend Framework** | Laravel | 11.x |
| **Frontend Framework** | Livewire + Alpine.js | Latest |
| **Styling** | Tailwind CSS | Latest |
| **Database** | MySQL | 8.0 |
| **Containerization** | Docker + Docker Compose | Latest |
| **Build Tool** | Vite | 6.x |
| **PHP Version** | PHP | 8.3+ |
| **Node.js** | Node.js | 18+ |

## 🏗️ Project Structure

```
inventory_jovanni/
├── app/
│   ├── Livewire/                # Livewire components
│   ├── Models/                  # Eloquent models
│   └── Services/                # Business logic
├── resources/
│   ├── views/                   # Blade templates
│   │   ├── components/          # Reusable UI components
│   │   ├── layouts/             # Page layouts
│   │   └── livewire/            # Livewire views
│   ├── css/                     # Stylesheets
│   └── js/                      # JavaScript files
├── public/
│   ├── images/                  # Static images
│   │   ├── jovanni_logo_black.png
│   │   └── jovanni_logo_white.png
│   └── build/                   # Compiled assets
├── database/
│   ├── migrations/              # Database schema
│   └── seeders/                 # Test data
├── docker-compose.yml           # Docker services
├── .env.example                 # Environment template
└── DEVELOPMENT_SETUP.md         # Complete setup guide
```

## 🎨 UI/UX Features

### Dynamic Theme System
- **Light/Dark Mode**: Automatic theme switching
- **Dynamic Logo**: Jovanni logo adapts to theme (black for light, white for dark)
- **Responsive Design**: Optimized for desktop, tablet, and mobile
- **Modern Components**: Professional UI with Tailwind CSS

### Key Components
- **Dashboard**: Real-time analytics and metrics
- **Inventory Management**: Product tracking and stock control
- **Customer Management**: Customer profiles and history
- **Branch Management**: Multi-location operations
- **Supplier Management**: Vendor relationships and procurement

## 🐳 Docker Configuration

The application uses Docker for database and development services:

```yaml
services:
  db:
    image: mysql:8.0
    ports: ["3307:3306"]
    environment:
      MYSQL_DATABASE: inventory_jovanni
      MYSQL_USER: jovanni
      MYSQL_PASSWORD: secret

  phpmyadmin:
    image: phpmyadmin/phpmyadmin:latest
    ports: ["8081:80"]
    environment:
      PMA_HOST: db
```

### Database Connection
- **Host**: 127.0.0.1
- **Port**: 3307
- **Database**: inventory_jovanni
- **Username**: jovanni
- **Password**: secret

## 🔧 Development Workflow

### Daily Development

```bash
# Start development environment
docker compose up -d db phpmyadmin
php artisan serve --host=0.0.0.0 --port=8000

# Frontend development (separate terminal)
npm run dev
```

### Common Commands

```bash
# Database operations
php artisan migrate                    # Run migrations
php artisan migrate:fresh --seed      # Reset with fresh data
php artisan db:seed                   # Seed test data

# Cache management
php artisan cache:clear              # Clear application cache
php artisan config:clear             # Clear configuration cache
php artisan route:clear              # Clear route cache

# Frontend development
npm run dev                          # Development with hot reload
npm run build                        # Production build
npm run build --watch               # Build and watch for changes
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run tests with coverage
php artisan test --coverage
```

## 📚 Documentation

| Document | Description |
|----------|-------------|
| [🚀 Development Setup Guide](./DEVELOPMENT_SETUP.md) | Complete setup and configuration guide |
| [🎨 UI Components](./docs/ui-components.md) | Component library and styling guide |
| [🗄️ Database Schema](./docs/database.md) | Database structure and relationships |
| [🔧 API Documentation](./docs/api.md) | API endpoints and usage |

## 🔄 Branch Strategy

- **`main`** - Production-ready code
- **`dev`** - Development branch with latest features
- **`feature/*`** - Feature development branches

### Development Process

1. **Feature Development** - Create feature branches from `dev`
2. **Pull Requests** - Submit PRs to `dev` branch
3. **Code Review** - Team review and testing
4. **Merge to Main** - Production deployment

## 🐛 Troubleshooting

### Common Issues

**Database Connection Issues:**
```bash
# Check Docker containers
docker compose ps

# Restart services
docker compose down && docker compose up -d db phpmyadmin
```

**Permission Issues:**
```bash
# Fix Laravel permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Frontend Build Issues:**
```bash
# Clear and reinstall
rm -rf node_modules package-lock.json
npm install
npm run build
```

## 🤝 Contributing

### Development Guidelines

1. **Code Style** - Follow Laravel coding standards
2. **Testing** - Write tests for new features
3. **Documentation** - Update relevant documentation
4. **Commits** - Use conventional commit messages
5. **Reviews** - All changes require peer review

### Commit Message Format

```
feat: add inventory dashboard component
fix: resolve logo display in dark mode
docs: update setup documentation
refactor: optimize database queries
```

## 📊 Current Status

### ✅ Completed Features

- **Dynamic Logo System** - Theme-aware logo switching
- **Database Setup** - MySQL with Docker configuration
- **Environment Configuration** - Comprehensive .env setup
- **Development Documentation** - Complete setup guide
- **Docker Integration** - Database and phpMyAdmin services
- **Responsive UI** - Mobile-optimized interface

### 🚧 In Development

- **Inventory Dashboard** - Real-time analytics
- **Customer Management** - Enhanced customer profiles
- **Branch Operations** - Multi-location management
- **Reporting System** - Advanced analytics and reports

### 📋 Roadmap

- **Mobile App** - React Native mobile application
- **API Integration** - RESTful API for third-party integrations
- **Advanced Analytics** - Business intelligence dashboard
- **Automated Reporting** - Scheduled report generation
- **Barcode Integration** - Product scanning capabilities

## 🆘 Support

### Getting Help

- **Issues**: [GitHub Issues](https://github.com/dGreatNoob/inventory_jovanni/issues)
- **Discussions**: [GitHub Discussions](https://github.com/dGreatNoob/inventory_jovanni/discussions)
- **Documentation**: Check the `/docs` directory
- **Setup Guide**: See [DEVELOPMENT_SETUP.md](./DEVELOPMENT_SETUP.md)

### Quick Links

- **Application**: http://localhost:8000
- **Database Admin**: http://localhost:8081
- **API Docs**: `/docs/api`
- **Database Schema**: `/docs/database`

## 🚀 Ready to Start?

```bash
# Clone and start developing
git clone https://github.com/dGreatNoob/inventory_jovanni.git
cd inventory_jovanni
git checkout dev

# Follow the setup guide
# See DEVELOPMENT_SETUP.md for complete instructions

# Start coding!
git checkout -b feature/your-awesome-feature
```

---

**🎒 Jovanni Bags** - Modern inventory management for the bag retail industry. Built with Laravel, powered by innovation.

**Made with ❤️ for bag retailers worldwide**