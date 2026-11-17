#!/bin/sh

PORT="${PORT:-8080}"
echo "Starting Laravel BookStore on port ${PORT}..."

# Dynamically set port in Nginx config
sed -i "s/listen 8080 default_server;/listen ${PORT} default_server;/g" /etc/nginx/http.d/default.conf || true
sed -i "s/listen \[::\]:8080 default_server;/listen \[::\]:${PORT} default_server;/g" /etc/nginx/http.d/default.conf || true

# Link storage
php artisan storage:link || true

# Run database migrations safely (does not abort if DB is warming up)
echo "Running migrations..."
php artisan migrate --force || echo "Warning: migrations will run when database connection is ready."

# Cache optimization (safe)
php artisan config:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start PHP-FPM
php-fpm -D

# Start Nginx
echo "Nginx listening on port ${PORT}..."
exec nginx -g "daemon off;"
