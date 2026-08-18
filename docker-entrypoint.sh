#!/bin/bash
set -e

echo "=== [AK-MART DOCKER BOOTSTRAP] ==="

# Wait for database if configured
echo "Checking database connection..."
php -r "
for (\$i = 0; \$i < 10; \$i++) {
    try {
        \$dbh = new PDO(
            getenv('DB_CONNECTION') === 'pgsql' ? 'pgsql:' . (getenv('DATABASE_URL') ?: 'host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE')) : 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        echo \"Database connected successfully!\\n\";
        break;
    } catch (Exception \$e) {
        echo \"Waiting for database... retry \" . (\$i + 1) . \"\\n\";
        sleep(2);
    }
}
" || true

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Run database seeders to populate all demo data
echo "Populating demo catalog, users, permissions, branches, and orders..."
php artisan db:seed --force || true

# Create storage symlink if not present
php artisan storage:link || true

# Clear cache and optimize for production
echo "Optimizing application cache..."
php artisan optimize:clear
php artisan view:cache || true
php artisan route:cache || true

echo "=== [AK-MART IS READY FOR TRAFFIC] ==="

# Start Apache in foreground
exec apache2-foreground
