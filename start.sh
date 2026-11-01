#!/bin/bash
set -e

echo "===================================="
echo "🚀 STARTING DEPLOYMENT SCRIPT"
echo "===================================="

echo ">>> Creating storage directories..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/app/public
mkdir -p storage/app/public/livewire-tmp
mkdir -p storage/app/livewire-tmp
mkdir -p storage/logs

echo ">>> Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo ">>> Recreating storage symlink..."
rm -rf public/storage
php artisan storage:link

echo ">>> Running migrations (non-destructive)..."
php artisan migrate --force

# Only seed on a truly empty database
USERS_COUNT=$(php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('users')->count();" 2>/dev/null || echo "0")
if [ "$USERS_COUNT" = "0" ]; then
  echo ">>> Seeding database (first run)..."
  php artisan db:seed --force
else
  echo ">>> Database already has data, skipping seed."
fi

echo ">>> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "===================================="
echo "✅ Done! Starting Apache..."
echo "===================================="

apache2-foreground
