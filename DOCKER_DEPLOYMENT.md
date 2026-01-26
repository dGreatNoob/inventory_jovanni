# Docker Deployment Guide

This guide explains how to deploy the Inventory Jovanni application using Docker, making it accessible on port 80 via the server's IP address.

## Prerequisites

- Docker Engine 20.10+ installed
- Docker Compose 2.0+ installed
- At least 2GB RAM available
- Port 80 available on the host machine

## Quick Start

### 1. Clone and Navigate to Project

```bash
cd /path/to/inventory_jovanni
```

### 2. Configure Environment Variables

Create a `.env` file from `.env.example`:

```bash
cp .env.example .env
```

Edit `.env` and configure the following important variables:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://YOUR_SERVER_IP

DB_DATABASE=inventory_jovanni
DB_USERNAME=root
DB_PASSWORD=your_secure_password
DB_ROOT_PASSWORD=your_secure_root_password

REDIS_HOST=redis
REDIS_PORT=6379
```

### 3. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

### 4. Build and Start Services

For development:
```bash
docker-compose up -d --build
```

For production:
```bash
docker-compose -f docker-compose.prod.yml up -d --build
```

### 5. Run Initial Setup (First Time Only)

```bash
# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate --force

# Create storage link
docker-compose exec app php artisan storage:link

# Cache configuration
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### 6. Access the Application

The application will be accessible at:
- **http://YOUR_SERVER_IP** (port 80)
- **http://localhost** (from the server itself)

## Service Details

### Services Running

1. **app** - PHP 8.2-FPM with Laravel application
2. **nginx** - Nginx web server (port 80)
3. **db** - MySQL 8.0 database (port 3307)
4. **redis** - Redis cache server (port 6380)
5. **phpmyadmin** - Database management (port 8081, optional)

### Port Mapping

- **80** → Nginx (Web Application)
- **443** → Nginx (HTTPS - configure SSL separately)
- **3307** → MySQL (Database)
- **6380** → Redis (Cache)
- **8081** → phpMyAdmin (Database Management)

## Common Operations

### View Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

### Stop Services

```bash
docker-compose down
```

### Restart Services

```bash
docker-compose restart
```

### Rebuild After Code Changes

```bash
docker-compose up -d --build
```

### Run Artisan Commands

```bash
docker-compose exec app php artisan [command]
```

### Access Container Shell

```bash
docker-compose exec app bash
```

### Database Access

```bash
# Via MySQL client
docker-compose exec db mysql -u root -p inventory_jovanni

# Via phpMyAdmin
# Access at http://YOUR_SERVER_IP:8081
```

## Production Deployment Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure secure database passwords
- [ ] Set `APP_URL` to your server's IP or domain
- [ ] Configure SSL/HTTPS (recommended for production)
- [ ] Set up firewall rules (allow port 80, 443)
- [ ] Configure automatic backups for database
- [ ] Set up log rotation
- [ ] Configure monitoring and alerts
- [ ] Test application accessibility from external network

## Security Considerations

1. **Change Default Passwords**: Update all default passwords in `.env`
2. **Firewall**: Only expose necessary ports (80, 443)
3. **SSL/TLS**: Configure HTTPS for production
4. **Database Access**: Restrict database port (3307) to internal network only
5. **phpMyAdmin**: Consider removing or restricting access to phpMyAdmin in production

## Troubleshooting

### Application Not Accessible

1. Check if containers are running:
   ```bash
   docker-compose ps
   ```

2. Check Nginx logs:
   ```bash
   docker-compose logs nginx
   ```

3. Verify port 80 is not in use:
   ```bash
   sudo netstat -tulpn | grep :80
   ```

4. Check firewall:
   ```bash
   sudo ufw status
   sudo ufw allow 80/tcp
   ```

### Database Connection Issues

1. Check database health:
   ```bash
   docker-compose exec db mysqladmin ping -h localhost -u root -prootsecret
   ```

2. Verify environment variables in `.env`

### Permission Issues

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Clear All Caches

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

## Network Access

To access the application from other machines on the network:

1. **Find Server IP Address**:
   ```bash
   ip addr show
   # or
   hostname -I
   ```

2. **Access from Client**: Open browser and navigate to `http://SERVER_IP`

3. **Firewall Configuration** (if needed):
   ```bash
   # Ubuntu/Debian
   sudo ufw allow 80/tcp
   sudo ufw reload
   
   # CentOS/RHEL
   sudo firewall-cmd --permanent --add-service=http
   sudo firewall-cmd --reload
   ```

## Maintenance

### Backup Database

```bash
docker-compose exec db mysqldump -u root -prootsecret inventory_jovanni > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Update Application

```bash
git pull
docker-compose exec app composer install --no-dev --optimize-autoloader
docker-compose exec app npm install && npm run build
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose restart
```

## Support

For issues or questions, check:
- Application logs: `docker-compose logs -f app`
- Nginx logs: `docker-compose logs -f nginx`
- Database logs: `docker-compose logs -f db`
