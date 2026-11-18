#!/usr/bin/env bash
set -e

echo "==> Preparing production environment..."
php artisan storage:link || true

if [ "${AUTORUN_MIGRATIONS}" = "true" ]; then
    echo "==> Running production database migrations (safe release step)..."
    php artisan migrate --force --no-interaction
fi

# Optimize Laravel configuration, routes, and views for production
if [ "${APP_ENV}" = "production" ]; then
    echo "==> Caching configuration, routes, and views..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> Starting production web server..."
# Check if php-fpm and nginx are installed in the container
if command -v php-fpm >/dev/null 2>&1 && command -v nginx >/dev/null 2>&1; then
    php-fpm -D
    exec nginx -g "daemon off;"
else
    exec php -S 0.0.0.0:${PORT:-8080} -t public/
fi
