#!/bin/bash
set -e

echo "===================================="
echo "🚀 STARTING DEPLOYMENT SCRIPT"
echo "===================================="

# 1. Ensure required storage directories exist
echo ">>> Creating storage directories..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/app/public
mkdir -p storage/app/public/livewire-tmp  # <-- THE EXACT FOLDER LIVEWIRE NEEDS
mkdir -p storage/app/livewire-tmp
mkdir -p storage/logs

# 2. Set proper permissions
echo ">>> Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 777 storage bootstrap/cache

# 3. Recreate the public storage symlink
echo ">>> Recreating storage symlink..."
rm -rf public/storage
php artisan storage:link

# 4. Run FRESH migrations and seeders
echo ">>> Running fresh migrations and seeders..."
php artisan migrate:fresh --seed --force

# 5. Cache config and routes
echo ">>> Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "===================================="
echo "✅ DEPLOYMENT SCRIPT FINISHED! Starting Apache..."
echo "===================================="

# 6. Start Apache
apache2-foreground
