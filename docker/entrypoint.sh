#!/usr/bin/env bash
set -e

echo "==> Caching configuration"
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "==> Running migrations"
php artisan migrate --force

echo "==> Starting supervisord"
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf