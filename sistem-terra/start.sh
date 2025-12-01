#!/bin/bash

#1. CLEAR CACHE (Prevent old cache issues)
php artisan optimize:clear

#2. PERBAIKI IZIN (Wajib untuk mencegah crash)
chmod -R 777 storage
chmod -R 777 bootstrap/cache

#3. STORAGE LINK untuk gambar dan assets
php artisan storage:link

#4. Install dependencies
composer install --no-dev --optimize-autoloader

#5. Wait for MySQL to be ready
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