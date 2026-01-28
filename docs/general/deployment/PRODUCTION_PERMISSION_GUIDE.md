# Production Permission Fix Guide

This guide helps you resolve permission issues in your Laravel application running on production.

## Quick Fix Scripts

### 1. Comprehensive Fix (Recommended)
```bash
sudo ./fix-production-permissions.sh
```
This script performs a complete permission fix including:
- Sets proper ownership and permissions
- Creates necessary directories
- Sets up storage symlink
- Optimizes Laravel application
- Verifies permissions

### 2. Quick Fix (Emergency)
```bash
sudo ./quick-permission-fix.sh
```
Use this for immediate permission fixes without optimization.

## Manual Permission Commands

If you prefer to run commands manually:

```bash
# Navigate to project directory
cd /var/www

# Set ownership
sudo chown -R www-data:www-data /var/www

# Set directory permissions
sudo find /var/www -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www -type f -exec chmod 644 {} \;

# Set writable permissions for Laravel directories
sudo chmod -R 775 storage bootstrap/cache

# Make artisan executable
sudo chmod +x artisan

# Create storage symlink
sudo -u www-data php artisan storage:link
```

## Common Permission Issues

### 1. Storage Directory Not Writable
**Error:** `Permission denied` when writing to storage
**Fix:** 
```bash
sudo chmod -R 775 storage
sudo chown -R www-data:www-data storage
```

### 2. Cache Directory Not Writable
**Error:** `Permission denied` when writing to bootstrap/cache
**Fix:**
```bash
sudo chmod -R 775 bootstrap/cache
sudo chown -R www-data:www-data bootstrap/cache
```

### 3. Artisan Not Executable
**Error:** `Permission denied` when running artisan commands
**Fix:**
```bash
sudo chmod +x artisan
```

### 4. Storage Symlink Missing
**Error:** Images/files not accessible via public URLs
**Fix:**
```bash
sudo -u www-data php artisan storage:link
```

## Directory Structure and Permissions

```
/var/www/
├── storage/           (775 - writable by web server)
│   ├── app/
│   ├── framework/
│   ├── logs/
│   └── ...
├── bootstrap/cache/   (775 - writable by web server)
├── public/           (755 - readable by web server)
├── vendor/           (755 - readable only)
├── artisan           (755 - executable)
└── .env              (640 - readable by web server only)
```

## Web Server User Verification

Check your web server user:
```bash
# For Nginx
ps aux | grep nginx

# For Apache
ps aux | grep apache

# Check process owner
ps aux | grep -E "(nginx|apache|php-fpm)"
```

## SELinux/AppArmor Issues

If you're still having permission issues, check for SELinux or AppArmor:

### SELinux
```bash
# Check SELinux status
sestatus

# If enabled, set context
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_read_user_content 1
```

### AppArmor
```bash
# Check AppArmor status
sudo aa-status

# If enabled, you may need to create profiles
```

## Troubleshooting Steps

1. **Run the comprehensive fix script**
2. **Check web server error logs**
3. **Verify web server user matches script settings**
4. **Check for SELinux/AppArmor restrictions**
5. **Verify disk space and inodes**
6. **Test with a simple PHP file**

## Testing Permissions

Create a test file to verify permissions:
```bash
# Create test file
echo "<?php phpinfo(); ?>" > /var/www/public/test.php

# Access via browser
# http://your-domain.com/test.php

# Remove test file
rm /var/www/public/test.php
```

## Important Notes

- Always run permission scripts as root or with sudo
- The scripts assume your web server runs as `www-data`
- Adjust the `WEB_USER` and `WEB_GROUP` variables if different
- Test thoroughly after making permission changes
- Keep backups before running permission scripts

## Emergency Recovery

If something goes wrong:
```bash
# Restore from backup
sudo rsync -av /path/to/backup/ /var/www/

# Or reset permissions to defaults
sudo chown -R root:root /var/www
sudo chmod -R 755 /var/www
sudo chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

## Support

If you continue to experience issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server logs
3. Verify PHP configuration
4. Test with a minimal Laravel installation
