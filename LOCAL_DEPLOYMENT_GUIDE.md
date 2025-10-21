# Local Deployment Guide (No Docker)

This guide will help you deploy the Inventory Jovanni application on your production server without Docker.

## Prerequisites

### System Requirements
- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 18+ and npm
- **MySQL**: 8.0 or higher
- **Redis**: 7+ (optional but recommended)
- **Web Server**: Nginx or Apache
- **Git**: For cloning the repository

### PHP Extensions Required
```bash
# Check if all required extensions are installed
php -m | grep -E "(pdo_mysql|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|fileinfo|gd|zip|curl|redis)"
```

If any are missing, install them:
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-redis php8.2-bcmath

# CentOS/RHEL
sudo yum install php-mysql php-mbstring php-xml php-curl php-zip php-gd php-redis php-bcmath

# Or using dnf on newer systems
sudo dnf install php-mysql php-mbstring php-xml php-curl php-zip php-gd php-redis php-bcmath
```

## Step-by-Step Installation

### 1. Clone the Repository
```bash
git clone <your-repository-url>
cd inventory_jovanni
```

### 2. Install PHP Dependencies
```bash
# Install Composer if not already installed
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install dependencies
composer install --no-dev --optimize-autoloader

# If you get memory errors, try:
composer install --no-dev --optimize-autoloader --no-scripts
```

### 3. Install Node.js Dependencies
```bash
# Install Node.js dependencies
npm install

# Build assets for production
npm run build
```

### 4. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup
```bash
# Create database
mysql -u root -p
```

In MySQL:
```sql
CREATE DATABASE inventory_jovanni;
CREATE USER 'jovanni'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON inventory_jovanni.* TO 'jovanni'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 6. Configure Environment Variables
Edit `.env` file:
```env
APP_NAME="Inventory Jovanni"
APP_ENV=production
APP_KEY=base64:your_generated_key
APP_DEBUG=false
APP_URL=http://your-domain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_jovanni
DB_USERNAME=jovanni
DB_PASSWORD=your_secure_password

# Cache Configuration
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Session Configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Queue Configuration
QUEUE_CONNECTION=redis
```

### 7. Run Database Migrations
```bash
# Run migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed
```

### 8. Set Permissions
```bash
# Set proper permissions
sudo chown -R www-data:www-data /path/to/inventory_jovanni
sudo chmod -R 755 /path/to/inventory_jovanni
sudo chmod -R 775 /path/to/inventory_jovanni/storage
sudo chmod -R 775 /path/to/inventory_jovanni/bootstrap/cache
```

### 9. Configure Web Server

#### Nginx Configuration
Create `/etc/nginx/sites-available/inventory-jovanni`:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/inventory_jovanni/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/inventory-jovanni /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### Apache Configuration
Create `.htaccess` in the public directory:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 10. Configure PHP-FPM
Edit `/etc/php/8.2/fpm/pool.d/www.conf`:
```ini
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

### 11. Configure Redis (Optional but Recommended)
```bash
# Install Redis
sudo apt install redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
```

Set password in redis.conf:
```
requirepass your_redis_password
```

Restart Redis:
```bash
sudo systemctl restart redis-server
```

### 12. Set Up Queue Worker (Optional)
Create a systemd service for queue worker:
```bash
sudo nano /etc/systemd/system/inventory-jovanni-worker.service
```

Add:
```ini
[Unit]
Description=Inventory Jovanni Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /path/to/inventory_jovanni/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
WorkingDirectory=/path/to/inventory_jovanni

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable inventory-jovanni-worker
sudo systemctl start inventory-jovanni-worker
```

## Troubleshooting Common Issues

### Composer Issues

#### Memory Limit Error
```bash
# Increase memory limit
php -d memory_limit=2G /usr/local/bin/composer install

# Or set in php.ini
echo "memory_limit = 2G" | sudo tee -a /etc/php/8.2/cli/php.ini
```

#### Permission Errors
```bash
# Fix composer cache permissions
sudo chown -R $USER:$USER ~/.composer
```

#### Extension Missing
```bash
# Check installed extensions
php -m

# Install missing extensions
sudo apt install php8.2-[extension-name]
```

### Laravel Issues

#### Application Key
```bash
php artisan key:generate
```

#### Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### Storage Link
```bash
php artisan storage:link
```

#### Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Issues

#### Connection Refused
- Check MySQL service: `sudo systemctl status mysql`
- Verify credentials in `.env`
- Check firewall: `sudo ufw status`

#### Migration Errors
```bash
# Reset migrations
php artisan migrate:fresh --seed
```

### Web Server Issues

#### 502 Bad Gateway
- Check PHP-FPM status: `sudo systemctl status php8.2-fpm`
- Check error logs: `sudo tail -f /var/log/nginx/error.log`

#### 404 Not Found
- Verify document root in nginx/apache config
- Check `.htaccess` file exists in public directory

## Security Considerations

1. **Change default passwords**
2. **Set up SSL certificate**
3. **Configure firewall**
4. **Regular security updates**
5. **Backup strategy**

## Performance Optimization

1. **Enable OPcache**
2. **Use Redis for caching**
3. **Configure nginx caching**
4. **Optimize database queries**
5. **Use CDN for static assets**

## Monitoring

1. **Set up log monitoring**
2. **Monitor server resources**
3. **Set up alerts**
4. **Regular backups**

## Quick Commands Reference

```bash
# Update application
git pull origin main
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate
php artisan config:cache

# Check status
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
sudo systemctl status redis-server

# View logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/php8.2-fpm.log
sudo tail -f /path/to/inventory_jovanni/storage/logs/laravel.log
```
