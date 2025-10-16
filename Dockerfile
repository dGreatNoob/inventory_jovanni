# syntax=docker/dockerfile:1

# --- Composer Dependencies Stage ---
FROM composer:2.7 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --ignore-platform-reqs --no-scripts --no-autoloader

# --- Node Build Stage ---
FROM node:20-alpine AS assets
WORKDIR /app

# Copy package files
COPY package.json package-lock.json ./
RUN npm ci --only=production

# Copy source files needed for build
COPY resources/ ./resources/
COPY public/ ./public/
COPY vite.config.js ./

# Build assets
RUN npm run build

# --- Production Stage ---
FROM php:8.2-fpm-alpine

# Install system dependencies in one layer
RUN apk add --no-cache \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    sqlite \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip

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

# Copy dependencies and built assets
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Install PHP dependencies (optimized)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

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