# 🚀 Local Setup Guide - CliqueHA Inventory Management System

## ✅ Setup Completed Successfully!

Your CliqueHA inventory management system is now running locally on your machine.

---

## 📋 System Overview

**Project**: CliqueHA (Gentle Walker) - Pet Supply Inventory Management  
**Framework**: Laravel 12.17.0 with Livewire  
**Database**: MySQL 8.0 (Docker Container)  
**Frontend**: Vite + Tailwind CSS + Alpine.js

---

## 🔧 Current Configuration

### Database (Docker)
- **Container**: `inventory-jovanni-db`
- **Host**: `127.0.0.1` (localhost)
- **Port**: `3307` (mapped from container port 3306)
- **Database Name**: `inventory_jovanni`
- **Username**: `jovanni`
- **Password**: `secret`
- **Status**: ✅ Running with migrations and seed data

### Database Management (phpMyAdmin)
- **URL**: http://localhost:8081
- **Container**: `inventory-jovanni-phpmyadmin`
- **Auto-login**: Configured with database credentials
- **Status**: ✅ Running and accessible

### Application Server
- **URL**: http://localhost:8000
- **Host**: 0.0.0.0 (accessible from network)
- **Port**: 8000
- **Status**: ✅ Running

### Frontend Assets
- **Build Tool**: Vite 6.3.5
- **Status**: ✅ Built and ready

---

## 🎯 How to Access the Application

### Main Application
Open your browser and navigate to:
```
http://localhost:8000
```

### Default Login Credentials
The database has been seeded with initial data including user accounts. Check the seeders for default credentials:
```bash
php artisan tinker
# Then query users:
User::all();
```

---

## 🛠️ Daily Development Workflow

### Starting the Application

1. **Start the Database** (if not already running):
```bash
cd /home/biiieem/repos/inventory_jovanni
docker compose up -d db
```

2. **Start Laravel Server**:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

3. **For Frontend Development** (in a separate terminal):
```bash
npm run dev
```
This starts Vite in watch mode for hot module replacement during development.

### Stopping the Application

1. **Stop Laravel Server**: Press `Ctrl+C` in the terminal where it's running

2. **Stop Docker Database** (optional):
```bash
docker compose down
```

---

## 📦 Installed Dependencies

### PHP Dependencies (Composer)
- ✅ Laravel Framework 12.0
- ✅ Livewire Flux & Volt
- ✅ Spatie Permission & Activity Log
- ✅ Laravel Reverb (WebSockets)
- ✅ QR Code Generator
- ✅ Pusher PHP Server

### Node Dependencies (npm)
- ✅ Vite 6.0
- ✅ Tailwind CSS 4.0
- ✅ Alpine.js (via CDN/Livewire)
- ✅ ApexCharts
- ✅ SweetAlert2
- ✅ Flowbite
- ✅ FontAwesome & Bootstrap Icons

---

## 🗄️ Database Structure

The system includes the following main modules:

### Core Tables
- `users` - User accounts with role-based access
- `departments` - Department management
- `permissions` & `roles` - Authorization system

### Inventory Management
- `supply_profiles` - Product catalog
- `supply_batches` - Batch tracking
- `stock_batches` - Stock management
- `item_types` & `item_classes` - Product categorization

### Purchase & Sales
- `suppliers` - Supplier management
- `purchase_orders` & `purchase_order_items` - Procurement
- `customers` - Customer database
- `sales_orders` & `sales_order_items` - Sales tracking
- `sales_returns` & `sales_return_items` - Returns processing

### Financial & Operations
- `finances` - Financial transactions
- `shipments` & `shipment_status_logs` - Delivery tracking
- `request_slips` - Internal requests
- `notifications` - System notifications
- `activity_log` - Audit trail

---

## 🔍 Useful Artisan Commands

### Database
```bash
# Check migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# Refresh database (WARNING: Deletes all data)
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_example_table
```

### Application
```bash
# Clear all caches
php artisan optimize:clear

# View routes
php artisan route:list

# Create new Livewire component
php artisan make:livewire ExampleComponent

# Run tests
php artisan test
```

### Queue & Jobs
```bash
# Run queue worker
php artisan queue:work

# List failed jobs
php artisan queue:failed
```

---

## 🐳 Docker Management

### View Running Containers
```bash
docker ps
```

### View Container Logs
```bash
docker compose logs db
docker compose logs -f db  # Follow logs
```

### Access MySQL Shell
```bash
# Via Docker
docker compose exec db mysql -u gentlewalker -psecret gentle_walker

# Via Host (port 3307)
mysql -h 127.0.0.1 -P 3307 -u gentlewalker -psecret gentle_walker
```

### Restart Database Container
```bash
docker compose restart db
```

---

## 🎨 Frontend Development

### Development Mode (Hot Reload)
```bash
npm run dev
```
This starts Vite dev server on http://localhost:5173 with hot module replacement.

### Production Build
```bash
npm run build
```
Compiles and minifies assets for production.

### Asset Locations
- **Source**: `resources/js/app.js`, `resources/css/app.css`
- **Built**: `public/build/`
- **Views**: `resources/views/`

---

## 🔐 Environment Variables

Current `.env` configuration:
```env
APP_NAME=Laravel
APP_ENV=development
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=gentle_walker
DB_USERNAME=gentlewalker
DB_PASSWORD=secret

QUEUE_CONNECTION=database
```

---

## 🚨 Troubleshooting

### Port Already in Use
If port 8000 is in use:
```bash
# Find process using port
lsof -i :8000

# Kill process
kill <PID>

# Or use different port
php artisan serve --port=8001
```

### Database Connection Failed
```bash
# Check if database container is running
docker ps | grep gentle-walker-db

# Restart database
docker compose restart db

# Check database logs
docker compose logs db
```

### Permission Errors
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

### Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

---

## 📚 Additional Resources

### Documentation Files
- `README.md` - Project overview and features
- `DASHBOARD_IMPLEMENTATION.md` - Dashboard implementation details
- `DASHBOARD_ENHANCEMENTS_COMPLETE.md` - Dashboard enhancement documentation

### Helper Scripts
- `setup-database.sh` - Database setup script (requires sudo)
- `setup-database.sql` - Manual database setup SQL

---

## 🎉 You're All Set!

Your CliqueHA inventory management system is fully configured and running!

**Access the application**: http://localhost:8000

### What's Running:
✅ MySQL Database (Docker container on port 3307)  
✅ Laravel Application Server (http://localhost:8000)  
✅ phpMyAdmin Database Management (http://localhost:8081)  
✅ Frontend Assets (compiled and ready)  
✅ Database with migrations and seed data  

### Next Steps:
1. Open http://localhost:8000 in your browser
2. Login with seeded user credentials
3. Explore the inventory management features
4. Review the documentation files for more details

---

## 📞 Need Help?

- Check the original README.md for feature documentation
- Review Laravel 11 documentation: https://laravel.com/docs/11.x
- Check Livewire documentation: https://livewire.laravel.com

---

*Setup completed on: September 30, 2025*
