# Laravel + Livewire Dockerfile
FROM php:8.2-fpm

# Install system dependencies
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
    netcat-openbsd \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Copy application code
COPY . .

# Create necessary directories
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Run post-install scripts
RUN php artisan package:discover --ansi

# Install Node dependencies and build assets
RUN npm install && npm run build

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
# Fix permissions\n\
echo "🔐 Setting proper permissions..."\n\
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache\n\
chmod -R 775 /var/www/storage /var/www/bootstrap/cache\n\
\n\
# Run migrations\n\
echo "📊 Running database migrations..."\n\
php artisan migrate --force\n\
\n\
# Create storage link if it doesn'\''t exist\n\
if [ ! -L /var/www/public/storage ]; then\n\
    echo "🔗 Creating storage link..."\n\
    php artisan storage:link\n\
fi\n\
\n\
# Clear and cache config\n\
echo "⚡ Optimizing application..."\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Check if we need to seed\n\
if [ "$DB_SEED" = "true" ]; then\n\
    echo "🌱 Seeding database..."\n\
    php artisan db:seed --force || echo "⚠️  Seeding failed"\n\
fi\n\
\n\
echo "✅ Laravel application ready!"\n\
\n\
exec "$@"' > /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose PHP-FPM port
EXPOSE 9000

# Use entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Start PHP-FPM
CMD ["php-fpm"]