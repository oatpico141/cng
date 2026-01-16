#!/bin/bash
echo "Starting application..."
php artisan migrate --force || true
php artisan config:clear || true
php artisan route:clear || true
echo "Starting server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}