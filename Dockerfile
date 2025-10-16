# syntax=docker/dockerfile:1

# --- Production Stage ---
FROM php:8.2-fpm

# Install system dependencies in one layer
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application code
COPY . .

# Create necessary directories
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Install Node dependencies and build assets
RUN npm install && npm run build

# Run post-install scripts
RUN php artisan package:discover --ansi

# Set permissions
RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]