#!/bin/bash

#1. Install dependencies
composer install --no-dev --optimize-autoloader

#2. Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! php artisan db:show > /dev/null 2>&1; do
    echo "MySQL is not ready yet, waiting 5 seconds..."
    sleep 5
done

echo "MySQL is ready!"

#5. Run database migrations
echo "Running database migrations..."
php artisan migrate --force

#6. Cache configuration and routes
php artisan config:cache
php artisan route:cache

#7. Start the Laravel server
echo "Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT