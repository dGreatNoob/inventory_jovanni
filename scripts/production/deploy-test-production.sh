#!/bin/bash

# 🚀 Jovanni Bags - Test Production Deployment Script
# This script prepares the application for test production deployment

echo "🚀 Starting Test Production Deployment..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from the Laravel project root directory"
    exit 1
fi

# 1. Install/Update Dependencies
echo "📦 Installing dependencies..."
composer install --optimize-autoloader --no-dev
npm install

# 2. Environment Setup
echo "🔧 Setting up environment..."
if [ ! -f ".env" ]; then
    echo "📋 Copying environment file..."
    cp .env.example .env
    echo "⚠️  Please update .env with your production database credentials"
    echo "   Then run: php artisan key:generate"
fi

# 3. Database Setup
echo "🗄️ Setting up database..."
php artisan migrate --force
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=SupplierSeeder
php artisan db:seed --class=ProductSeeder

# 4. Storage Setup
echo "📁 Setting up storage..."
php artisan storage:link

# 5. Cache & Optimization
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 6. Frontend Assets
echo "🎨 Building frontend assets..."
npm run build

# 7. Set Permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# 8. Final Checks
echo "✅ Running final checks..."
php artisan about

echo ""
echo "🎉 Test Production Deployment Complete!"
echo ""
echo "📋 Next Steps:"
echo "   1. Update .env with production database credentials"
echo "   2. Run: php artisan key:generate"
echo "   3. Configure your web server (Apache/Nginx)"
echo "   4. Set up SSL certificate"
echo "   5. Configure domain and DNS"
echo ""
echo "🔗 Access your application at: http://your-domain.com"
echo "📊 Check production readiness: See PRODUCTION_READINESS.md"
echo ""
echo "⚠️  Note: Only Product Management Module is ready for production"
echo "   Other modules are under revision and not recommended for production use"
