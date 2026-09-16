#!/usr/bin/env bash
set -e

echo "==> Caching configuration"
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "==> Running migrations"
php artisan migrate --force

if [[ "${WEB_SERVER:-nginx}" == "serve" ]]; then
    echo "==> Starting Laravel dev server (php artisan serve :${APP_PORT:-8001})"
    exec php artisan serve --host=0.0.0.0 --port="${APP_PORT:-8001}"
fi

echo "==> Starting supervisord (nginx + php-fpm)"
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf