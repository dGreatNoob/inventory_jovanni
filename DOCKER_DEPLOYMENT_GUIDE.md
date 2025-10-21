# Docker Deployment Guide

## Overview
This guide will help you deploy the Inventory Jovanni application using Docker.

## Prerequisites
- Docker and Docker Compose installed
- Git (to clone the repository)

## Quick Start

### 1. Clone and Setup
```bash
git clone <repository-url>
cd inventory_jovanni
```

### 2. Environment Configuration
Create a `.env` file from the example:
```bash
cp .env.example .env
```

The `.env` file should contain:
```env
APP_NAME="Inventory Jovanni"
APP_ENV=production
APP_KEY=base64:wOtTC9tc+PMFkmrGZb4FpoNJ4jDrfoA+iYFkzckOhF0=
APP_DEBUG=false

APP_URL=http://localhost
VITE_DEV_SERVER_URL=

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=inventory_jovanni
DB_USERNAME=jovanni
DB_PASSWORD=secret

# Redis Configuration
REDIS_HOST=redis
REDIS_PASSWORD=redis
REDIS_PORT=6379

# Reverb Configuration
REVERB_HOST=reverb
REVERB_PORT=8080
```

### 3. Deploy with Docker Compose
```bash
# Build and start all services
docker-compose up -d --build

# Run database migrations
docker-compose exec app php artisan migrate

# Seed the database (optional)
docker-compose exec app php artisan db:seed

# Generate application key (if needed)
docker-compose exec app php artisan key:generate
```

### 4. Access the Application
- **Web Application**: http://localhost
- **phpMyAdmin**: http://localhost:8081
- **Reverb WebSocket**: ws://localhost:8080

## Services Included

### 1. Nginx (Port 80, 443)
- Web server and reverse proxy
- Serves static files and proxies PHP requests to the app container
- Includes security headers and gzip compression

### 2. App (PHP-FPM)
- Laravel application running on PHP 8.2-FPM
- Includes all necessary PHP extensions
- Optimized for production

### 3. MySQL (Port 3307)
- Database server (MySQL 8.0)
- Database: `inventory_jovanni`
- User: `jovanni`
- Password: `secret`

### 4. Redis (Port 6379)
- Caching and session storage
- Queue backend
- Password: `redis`

### 5. Reverb (Port 8080)
- Laravel Reverb for real-time features
- WebSocket server

### 6. phpMyAdmin (Port 8081)
- Database management interface
- Access: http://localhost:8081

## Production Considerations

### Security
1. Change default passwords in production
2. Use environment variables for sensitive data
3. Enable HTTPS with SSL certificates
4. Configure firewall rules

### Performance
1. Enable OPcache in PHP
2. Use Redis for caching and sessions
3. Configure nginx caching
4. Use CDN for static assets

### Monitoring
1. Set up log aggregation
2. Monitor container health
3. Set up backup strategies
4. Configure alerts

## Troubleshooting

### Common Issues

1. **Permission Issues**
   ```bash
   docker-compose exec app chown -R www-data:www-data /var/www/storage
   ```

2. **Database Connection Issues**
   ```bash
   # Check if database is running
   docker-compose ps db
   
   # Check database logs
   docker-compose logs db
   ```

3. **Application Not Loading**
   ```bash
   # Check application logs
   docker-compose logs app
   
   # Check nginx logs
   docker-compose logs nginx
   ```

4. **Clear Cache**
   ```bash
   docker-compose exec app php artisan cache:clear
   docker-compose exec app php artisan config:clear
   docker-compose exec app php artisan view:clear
   ```

### Useful Commands

```bash
# View logs
docker-compose logs -f [service_name]

# Execute commands in container
docker-compose exec app php artisan [command]

# Restart services
docker-compose restart [service_name]

# Stop all services
docker-compose down

# Remove volumes (WARNING: This will delete all data)
docker-compose down -v
```

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_ENV` | Application environment | `production` |
| `APP_DEBUG` | Debug mode | `false` |
| `DB_DATABASE` | Database name | `inventory_jovanni` |
| `DB_USERNAME` | Database user | `jovanni` |
| `DB_PASSWORD` | Database password | `secret` |
| `REDIS_PASSWORD` | Redis password | `redis` |

## Next Steps

1. Configure your domain name
2. Set up SSL certificates
3. Configure backup strategies
4. Set up monitoring and logging
5. Configure CI/CD pipeline
