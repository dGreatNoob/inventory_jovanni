#!/bin/sh
set -e

# Fix permissions for Laravel storage and cache
chown -R www-data:www-data /usr/share/nginx/html/storage /usr/share/nginx/html/bootstrap/cache || true
chmod -R 775 /usr/share/nginx/html/storage /usr/share/nginx/html/bootstrap/cache || true

exec "$@" 