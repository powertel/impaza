#!/bin/sh
set -e

# Function to check if a directory is empty
is_empty() {
    [ -z "$(ls -A "$1" 2>/dev/null)" ]
}

# If vendor directory is empty, run composer install
if [ ! -d "/var/www/html/vendor" ] || is_empty "/var/www/html/vendor"; then
    echo "Vendor directory is empty. Running composer install..."
    composer install --no-interaction --no-progress --prefer-dist --no-dev
fi

# Ensure storage and bootstrap/cache permissions
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# Remove stale discovery and config files if they exist to prevent errors during artisan commands
echo "Removing stale cache files..."
rm -f bootstrap/cache/config.php bootstrap/cache/packages.php bootstrap/cache/routes.php bootstrap/cache/services.php

# Generate APP_KEY if it's not set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set. Generating one..."
    # We can't easily edit .env.prod here because it might be read-only or we don't want to persist it back to host
    # But we can set it for the current process if we use a wrapper or just generate it if missing in the environment
    # Laravel's key:generate usually writes to .env
    php artisan key:generate --force --no-interaction
fi

# Clear caches to avoid stale discovery files
echo "Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Re-optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Execute the original CMD
exec "$@"
