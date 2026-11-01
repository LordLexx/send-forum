#!/bin/bash
set -e

echo "===================================="
echo "🚀 STARTING DEPLOYMENT SCRIPT"
echo "===================================="

# 1. Storage directories
echo ">>> Creating storage directories..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/app/public
mkdir -p storage/app/public/livewire-tmp
mkdir -p storage/app/livewire-tmp
mkdir -p storage/logs

# 2. Permissions
echo ">>> Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 3. Storage symlink
echo ">>> Recreating storage symlink..."
rm -rf public/storage
php artisan storage:link

# 4. Wait for the database to be reachable (max ~60s)
echo ">>> Waiting for database..."
for i in $(seq 1 30); do
  if php artisan db:monitor > /dev/null 2>&1; then
    echo ">>> Database is reachable."
    break
  fi
  echo "    ($i/30) DB not ready yet, retrying in 2s..."
  sleep 2
done

# 5. Run migrations (NON-destructive) + seeders only on first deploy
echo ">>> Running migrations..."
php artisan migrate --force

# Only seed if the users table is empty (prevents wiping data on restarts)
if php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('users')->count();" 2>/dev/null | grep -q '^0$'; then
  echo ">>> Seeding database (first run)..."
  php artisan db:seed --force
else
  echo ">>> Database already seeded, skipping."
fi

# 6. Cache config, routes, views
echo ">>> Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "===================================="
echo "✅ DEPLOYMENT SCRIPT FINISHED! Starting Apache..."
echo "===================================="

# 7. Start Apache
apache2-foreground
