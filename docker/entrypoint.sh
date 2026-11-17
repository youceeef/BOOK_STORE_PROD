#!/bin/sh
set -e

# Link storage if not already linked
php artisan storage:link || true

# Run database migrations automatically in production
if [ "$AUTORUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
fi

# Cache configuration, routes, and views for high performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM in background and Nginx in foreground
php-fpm -D
exec nginx -g "daemon off;"
