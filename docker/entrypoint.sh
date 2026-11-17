#!/bin/sh
set -e

# Always ensure we are in the application root directory
cd /var/www/html

PORT="${PORT:-8080}"
echo "=========================================="
echo "==> Starting Laravel BookStore"
echo "==> Working directory: $(pwd)"
echo "==> Binding to: 0.0.0.0:${PORT}"
echo "=========================================="

# Storage link
php artisan storage:link || true

# Run migrations asynchronously in background so server is never delayed
(
    echo "==> Background worker: Waiting 3s before checking migrations..."
    sleep 3
    php artisan migrate --force && php artisan db:seed --force || echo "==> Migrations will run when database connection is ready."
) &

# Run Laravel HTTP server directly on 0.0.0.0:$PORT
echo "==> Server starting now on port ${PORT}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
