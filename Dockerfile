FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create start script (skip seeder - data already exists)
RUN echo '#!/bin/bash\n\
echo "Starting application..."\n\
php artisan migrate --force || true\n\
php artisan config:clear\n\
php artisan route:clear\n\
echo "Starting server on port ${PORT:-8080}..."\n\
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}\n\
' > /var/www/html/start.sh && chmod +x /var/www/html/start.sh

# Expose port
EXPOSE 8080

# Start the application
CMD ["/bin/bash", "/var/www/html/start.sh"]