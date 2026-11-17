#!/bin/sh

PORT="${PORT:-8080}"
echo "==> Configuring Nginx to listen on port ${PORT}..."

# Create runtime directories required by Alpine Nginx
mkdir -p /run/nginx /var/log/nginx
chown -R www-data:www-data /run/nginx /var/log/nginx

# Dynamically set port in Nginx config
sed -i "s/listen 8080;/listen ${PORT};/g" /etc/nginx/http.d/default.conf || true
sed -i "s/listen \[::\]:8080;/listen \[::\]:${PORT};/g" /etc/nginx/http.d/default.conf || true

# Storage link
php artisan storage:link || true

# Ensure permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start PHP-FPM in background
echo "==> Starting PHP-FPM on 127.0.0.1:9000..."
php-fpm -D

# Test Nginx syntax
nginx -t

# Run migrations asynchronously in background so Nginx is never delayed
(
    echo "==> Background worker: Waiting 5s before running migrations..."
    sleep 5
    php artisan migrate --force && php artisan db:seed --force || echo "==> Migrations will run when database connection is ready."
) &

# Start Nginx in foreground to serve traffic immediately
echo "==> Starting Nginx on port ${PORT}..."
exec nginx -g "daemon off;"
