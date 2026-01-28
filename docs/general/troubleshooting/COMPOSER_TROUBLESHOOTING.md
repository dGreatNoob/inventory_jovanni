# Composer Troubleshooting Guide

This guide addresses common Composer installation issues on production servers.

## Common Composer Errors and Solutions

### 1. Memory Limit Exceeded

**Error:**
```
Fatal error: Allowed memory size of X bytes exhausted
```

**Solutions:**
```bash
# Method 1: Increase memory limit temporarily
php -d memory_limit=2G /usr/local/bin/composer install

# Method 2: Set memory limit in php.ini
echo "memory_limit = 2G" | sudo tee -a /etc/php/8.2/cli/php.ini

# Method 3: Use composer with memory limit
COMPOSER_MEMORY_LIMIT=2G composer install

# Method 4: Install without scripts first
composer install --no-scripts
composer run-script post-install-cmd
```

### 2. Permission Denied Errors

**Error:**
```
Could not write to cache directory
```

**Solutions:**
```bash
# Fix composer cache permissions
sudo chown -R $USER:$USER ~/.composer
sudo chmod -R 755 ~/.composer

# Or change composer cache directory
export COMPOSER_CACHE_DIR=/tmp/composer-cache
mkdir -p /tmp/composer-cache
chmod 777 /tmp/composer-cache
```

### 3. Extension Missing Errors

**Error:**
```
The requested PHP extension ext-xyz is missing
```

**Solutions:**
```bash
# Check installed extensions
php -m

# Install missing extensions
sudo apt install php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-redis php8.2-bcmath

# For specific extensions mentioned in composer.json:
# ext-gd -> php8.2-gd
# ext-mbstring -> php8.2-mbstring
# ext-xml -> php8.2-xml
# ext-curl -> php8.2-curl
# ext-zip -> php8.2-zip
# ext-pdo_mysql -> php8.2-mysql
# ext-redis -> php8.2-redis
# ext-bcmath -> php8.2-bcmath
```

### 4. Network/Download Issues

**Error:**
```
Could not resolve host: packagist.org
```

**Solutions:**
```bash
# Check internet connectivity
ping packagist.org

# Try with different DNS
echo "nameserver 8.8.8.8" | sudo tee /etc/resolv.conf

# Use different repository
composer config -g repo.packagist composer https://packagist.org

# Or use mirror
composer config -g repo.packagist composer https://packagist.jp
```

### 5. SSL Certificate Issues

**Error:**
```
SSL certificate problem: unable to get local issuer certificate
```

**Solutions:**
```bash
# Disable SSL verification (not recommended for production)
composer config -g disable-tls true
composer config -g secure-http false

# Or update CA certificates
sudo apt update
sudo apt install ca-certificates
sudo update-ca-certificates
```

### 6. Composer Not Found

**Error:**
```
composer: command not found
```

**Solutions:**
```bash
# Install Composer globally
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Or install via package manager
sudo apt install composer

# Or use local installation
php composer.phar install
```

### 7. Version Conflicts

**Error:**
```
Your requirements could not be resolved to an installable set of packages
```

**Solutions:**
```bash
# Update composer
composer self-update

# Clear composer cache
composer clear-cache

# Try with different version constraints
composer install --ignore-platform-reqs

# Check for conflicts
composer why-not package/name
```

### 8. Platform Requirements

**Error:**
```
Package has requirements that could not be resolved
```

**Solutions:**
```bash
# Ignore platform requirements
composer install --ignore-platform-reqs

# Check platform requirements
composer check-platform-reqs

# Install specific platform packages
composer install --ignore-platform-reqs --no-dev
```

## Production-Specific Solutions

### 1. Optimize for Production

```bash
# Install without dev dependencies
composer install --no-dev --optimize-autoloader --no-scripts

# Run scripts separately
composer run-script post-install-cmd

# Clear cache
composer clear-cache
```

### 2. Use Composer in Docker

If you're having persistent issues, consider using Docker for Composer:

```bash
# Run composer in Docker container
docker run --rm -v $(pwd):/app composer install --no-dev --optimize-autoloader
```

### 3. Pre-download Dependencies

```bash
# Download dependencies on a machine with good internet
composer install --no-dev --optimize-autoloader

# Copy vendor directory to production server
rsync -av vendor/ production-server:/path/to/app/vendor/
```

## Environment-Specific Issues

### Ubuntu/Debian Systems

```bash
# Update package lists
sudo apt update

# Install required packages
sudo apt install php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-redis php8.2-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### CentOS/RHEL Systems

```bash
# Install EPEL repository
sudo yum install epel-release

# Install required packages
sudo yum install php-cli php-mysql php-mbstring php-xml php-curl php-zip php-gd php-redis php-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Alpine Linux

```bash
# Install required packages
apk add php8-cli php8-mysql php8-mbstring php8-xml php8-curl php8-zip php8-gd php8-redis php8-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

## Debugging Commands

```bash
# Check PHP version and extensions
php -v
php -m

# Check Composer version
composer --version

# Check Composer configuration
composer config --list

# Verbose output
composer install -vvv

# Check for conflicts
composer why-not package/name

# Validate composer.json
composer validate

# Check platform requirements
composer check-platform-reqs
```

## Quick Fix Script

Create a file called `fix-composer.sh`:

```bash
#!/bin/bash

echo "Fixing Composer issues..."

# Increase memory limit
echo "memory_limit = 2G" | sudo tee -a /etc/php/8.2/cli/php.ini

# Fix permissions
sudo chown -R $USER:$USER ~/.composer
sudo chmod -R 755 ~/.composer

# Clear cache
composer clear-cache

# Update Composer
composer self-update

# Install with increased memory
php -d memory_limit=2G /usr/local/bin/composer install --no-dev --optimize-autoloader --no-scripts

echo "Composer issues fixed!"
```

Make it executable and run:
```bash
chmod +x fix-composer.sh
./fix-composer.sh
```

## Still Having Issues?

1. **Check system logs**: `sudo tail -f /var/log/syslog`
2. **Check PHP logs**: `sudo tail -f /var/log/php8.2-fpm.log`
3. **Check Composer logs**: `composer install -vvv`
4. **Try minimal installation**: `composer install --no-dev --no-scripts`
5. **Contact system administrator** for server-specific issues
