FROM php:8.4-fpm

# 1. Dependencias del sistema: git/zip/unzip (composer), libpq-dev (driver pgsql),
#    nginx (servidor web), supervisor (mantener 2 procesos vivos)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git zip unzip libpq-dev libzip-dev nginx supervisor curl \
    && rm -rf /var/lib/apt/lists/*

# 2. Extensiones PHP: pgsql + pdo_pgsql (PostgreSQL) y zip (BackupService / ZipArchive)
RUN docker-php-ext-install pdo_pgsql pgsql zip

# 3. Composer desde su imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Código de la aplicación
WORKDIR /var/www/html
COPY . .

# 5. Config por defecto sin secretos (las env vars de Render prevalecen sobre este)
RUN cp .env.example .env

# La app usa PostgreSQL (índices GIN/jsonb). Si se corre sin BD se debe conectar a una.
ENV DB_CONNECTION=pgsql

# 6. Dependencias de producción (sin dev: sin sail, pint, pail...)
RUN composer install --no-dev --optimize-autoloader \
    && php artisan key:generate --force

# 7. Permisos: los procesos web escriben en storage/ y bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && mkdir -p /run/php \
    && chown www-data:www-data /run/php

# 8. Configuración interna del contenedor
COPY docker/default.conf /etc/nginx/sites-available/default
COPY docker/phpfpm.conf /usr/local/etc/php-fpm.d/zz-snippets.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/snippets.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
CMD ["/usr/local/bin/entrypoint.sh"]