# Production Deployment Guide

## Overview
This guide covers deploying the Inventory Jovanni application to production using Docker with proper image upload handling.

## Key Differences from Development

### Development vs Production

| Aspect | Development | Production |
|--------|-------------|------------|
| **Web Server** | `php artisan serve` | nginx + PHP-FPM |
| **Database** | Dockerized MySQL | Dockerized MySQL |
| **Image Storage** | Local storage | Persistent Docker volumes |
| **Caching** | File-based | Redis |
| **Sessions** | File-based | Redis |
| **Debug Mode** | Enabled | Disabled |
| **Asset Compilation** | Development | Production optimized |

## Image Upload Considerations

### 1. Storage Configuration
- **Development**: Images stored in `storage/app/public/photos/`
- **Production**: Images stored in persistent Docker volume `image_uploads`
- **Web Access**: Images served via nginx at `/storage/photos/`

### 2. File Permissions
```bash
# Set proper permissions for image uploads
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 3. Nginx Configuration
The production nginx config includes:
- Special handling for image files (jpg, jpeg, png, gif, webp)
- Proper caching headers for images
- Security headers to prevent image hotlinking

### 4. Docker Volumes
```yaml
volumes:
  image_uploads:
    driver: local
```

## Deployment Steps

### 1. Prerequisites
- Docker and Docker Compose installed
- Sufficient disk space for images
- Proper file permissions

### 2. Deploy to Production
```bash
# Run the production deployment script
./deploy-production.sh
```

### 3. Manual Deployment (Alternative)
```bash
# Stop existing containers
docker-compose -f docker-compose.prod.yml down

# Build and start services
docker-compose -f docker-compose.prod.yml up -d --build

# Create storage link
docker-compose -f docker-compose.prod.yml exec app php artisan storage:link

# Set permissions
docker-compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/storage
docker-compose -f docker-compose.prod.yml exec app chmod -R 775 /var/www/storage
```

## Environment Configuration

### Production Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=inventory_jovanni
DB_USERNAME=jovanni
DB_PASSWORD=your-secure-password

# Cache and Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
REDIS_PORT=6379

# File Storage
FILESYSTEM_DISK=local
```

## Image Upload Features

### 1. Supported Formats
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- WebP (.webp)

### 2. File Size Limits
- Maximum upload size: 10MB per image
- Configurable via `MAX_UPLOAD_SIZE` environment variable

### 3. Image Processing
- Automatic thumbnail generation
- Image optimization and compression
- Primary image selection

### 4. Storage Structure
```
storage/app/public/photos/
├── [uuid].jpg          # Original images
├── thumbnails/
│   └── [uuid]_thumb.jpg # Thumbnails
└── .gitignore
```

## Security Considerations

### 1. File Upload Security
- File type validation
- File size limits
- Secure filename generation (UUID)
- No direct file execution

### 2. Nginx Security
- Deny access to sensitive directories
- Proper MIME type handling
- Security headers for images

### 3. Docker Security
- Non-root user in containers
- Read-only volumes where possible
- Proper file permissions

## Monitoring and Maintenance

### 1. Log Monitoring
```bash
# View application logs
docker-compose -f docker-compose.prod.yml logs -f app

# View nginx logs
docker-compose -f docker-compose.prod.yml logs -f nginx
```

### 2. Storage Monitoring
```bash
# Check image upload volume usage
docker volume ls
docker system df -v
```

### 3. Backup Strategy
- Database backups: Regular MySQL dumps
- Image backups: Volume snapshots or rsync
- Configuration backups: Git repository

## Troubleshooting

### Common Issues

#### 1. Images Not Displaying
- Check storage link: `php artisan storage:link`
- Verify file permissions
- Check nginx configuration

#### 2. Upload Failures
- Check file size limits
- Verify directory permissions
- Check PHP upload limits

#### 3. Performance Issues
- Enable nginx caching
- Optimize images
- Use CDN for static assets

### Debug Commands
```bash
# Check container status
docker-compose -f docker-compose.prod.yml ps

# Access application container
docker-compose -f docker-compose.prod.yml exec app bash

# Check storage permissions
docker-compose -f docker-compose.prod.yml exec app ls -la /var/www/storage/app/public/

# Test image upload
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
```

## Performance Optimization

### 1. Image Optimization
- Enable image compression
- Generate multiple sizes
- Use WebP format when possible

### 2. Caching
- Redis for sessions and cache
- Nginx caching for static assets
- Browser caching for images

### 3. CDN Integration
- Consider using a CDN for image delivery
- Implement image resizing service
- Use lazy loading for images

## Scaling Considerations

### 1. Horizontal Scaling
- Use shared storage for images
- Implement load balancing
- Database replication

### 2. Storage Scaling
- Move to cloud storage (S3, etc.)
- Implement image CDN
- Use dedicated file servers

This production setup ensures reliable image uploads, proper security, and optimal performance for the Inventory Jovanni application.
