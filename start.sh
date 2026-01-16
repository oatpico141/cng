#!/bin/bash
set -e

echo "Starting application..."

# Set default port
export PORT=${PORT:-8080}
echo "Using PORT: $PORT"

# Generate nginx config from template
envsubst '${PORT}' < /var/www/html/docker/nginx.conf.template > /etc/nginx/sites-available/default

# Run migrations
echo "Running migrations..."
php /var/www/html/artisan migrate --force || true

# Clear cache
echo "Clearing cache..."
php /var/www/html/artisan config:clear || true
php /var/www/html/artisan route:clear || true

# Start supervisor
echo "Starting Nginx + PHP-FPM..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
