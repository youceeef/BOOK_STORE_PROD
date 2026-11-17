#!/bin/sh

PORT="${PORT:-8080}"
echo "==> Starting Laravel BookStore on 0.0.0.0:${PORT}..."

# Storage link
php artisan storage:link || true

# Run migrations asynchronously in background
(
    echo "==> Background: Waiting 3s before running migrations..."
    sleep 3
    php artisan migrate --force && php artisan db:seed --force || echo "==> Migrations will run when database connection is ready."
) &

# Run Laravel HTTP server directly on 0.0.0.0:$PORT
echo "==> Server listening on 0.0.0.0:${PORT}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
