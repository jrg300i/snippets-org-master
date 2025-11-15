#!/bin/bash
composer install
npm install --production
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache