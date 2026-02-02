# Laravel + Livewire Dockerfile
FROM php:8.3-fpm

# Install system dependencies (libwebp-dev required for WebP image uploads)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
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
    netcat-openbsd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Copy package files first for better layer caching
COPY package*.json ./
COPY composer.json composer.lock ./

# Install Node dependencies (cached layer if package.json doesn't change)
RUN npm ci --only=production

# Install PHP dependencies (cached layer if composer.json doesn't change)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy application code (this layer changes most frequently)
COPY . .

# Create necessary directories
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache

# Build assets during image creation
RUN npm run build

# Run post-install scripts
RUN php artisan package:discover --ansi

# PHP upload limits for file uploads (product images, Livewire)
COPY php/local.ini /usr/local/etc/php/conf.d/99-upload-limits.ini

# Set permissions
RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Create entrypoint script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "🚀 Starting Laravel application..."\n\
\n\
# Wait for MySQL\n\
echo "⏳ Waiting for database connection..."\n\
while ! nc -z db 3306; do\n\
  sleep 1\n\
done\n\
echo "✅ Database connection established"\n\
\n\
# Wait for Redis\n\
echo "⏳ Waiting for Redis connection..."\n\
while ! nc -z redis 6379; do\n\
  sleep 1\n\
done\n\
echo "✅ Redis connection established"\n\
\n\
# Generate application key if not set\n\
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then\n\
    echo "🔑 Generating application key..."\n\
    php artisan key:generate --force\n\
fi\n\
\n\
# Run migrations (only if RUN_MIGRATIONS is set to true)\n\
if [ "$RUN_MIGRATIONS" = "true" ]; then\n\
    echo "📊 Running database migrations..."\n\
    php artisan migrate --force\n\
else\n\
    echo "⏭️  Skipping migrations (set RUN_MIGRATIONS=true to run)"\n\
fi\n\
\n\
# Create storage link\n\
echo "🔗 Creating storage link..."\n\
php artisan storage:link\n\
\n\
# Build assets if manifest doesn't exist\n\
if [ ! -f "public/build/manifest.json" ]; then\n\
    echo "📦 Building frontend assets..."\n\
    npm run build || echo "⚠️  Asset build failed, but continuing..."\n\
fi\n\
\n\
# Clear config cache to ensure fresh read from .env\n\
echo "⚡ Optimizing application..."\n\
php artisan config:clear || true\n\
# Don't cache config here - let it be done by deployment script after .env is set\n\
php artisan route:cache || true\n\
php artisan view:cache || true\n\
\n\
echo "✅ Laravel application ready!"\n\
\n\
# Start PHP-FPM in foreground (required for Docker)\n\
exec php-fpm -F' > /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose PHP-FPM port
EXPOSE 9000

# Use entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# No CMD needed - entrypoint handles php-fpm startup with -F flag