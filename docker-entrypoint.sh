#!/bin/bash
set -e

# Run database migrations and seeding on container boot
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
    echo "Running database seeder..."
    php artisan db:seed --force || true
fi

# Clear and optimize configuration
php artisan optimize:clear
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Apache in foreground
exec apache2-foreground
