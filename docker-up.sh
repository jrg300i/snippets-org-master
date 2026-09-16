#!/usr/bin/env bash
set -e

IMAGE="snippets-org-test"
NETWORK="snippets-net"
DB_NAME="snipets_db"
DB_USER="postgres"
DB_PASS="123456"
APP_PORT="8002"
LAN_IP="${LAN_IP:-172.31.90.249}"

echo "==> 1) Red Docker (la app y la BD se hablan entre sí)"
docker network create "$NETWORK" 2>/dev/null || true

echo "==> 2) Contenedor PostgreSQL (la base de datos)"
if ! docker ps --format '{{.Names}}' | grep -q '^snip-db$'; then
    docker rm -f snip-db >/dev/null 2>&1 || true
    docker run -d --name snip-db --network "$NETWORK" \
        -e POSTGRES_DB="$DB_NAME" \
        -e POSTGRES_USER="$DB_USER" \
        -e POSTGRES_PASSWORD="$DB_PASS" \
        postgres:16 >/dev/null
    echo "    Base de datos arrancando... (esperando que esté lista)"
    sleep 6
else
    echo "    snip-db ya está corriendo."
fi

echo "==> 3) Construir la imagen de la app (Dockerfile)"
docker build -t "$IMAGE" .

echo "==> 4) Contenedor de la app (php artisan serve, visible en la red local)"
docker rm -f snip-app >/dev/null 2>&1 || true
docker run -d --name snip-app --network "$NETWORK" -p "$APP_PORT:8001" \
    -e APP_ENV=local \
    -e APP_DEBUG=true \
    -e APP_URL="http://$LAN_IP:$APP_PORT" \
    -e WEB_SERVER=serve \
    -e APP_PORT="$APP_PORT" \
    -e DB_CONNECTION=pgsql \
    -e DB_HOST=snip-db \
    -e DB_PORT=5432 \
    -e DB_DATABASE="$DB_NAME" \
    -e DB_USERNAME="$DB_USER" \
    -e DB_PASSWORD="$DB_PASS" \
    -e SESSION_DRIVER=database \
    -e CACHE_STORE=database \
    -e QUEUE_CONNECTION=sync \
    "$IMAGE" >/dev/null

echo ""
echo "✔ Listo. Tu app está en:"
echo "  - Local:        http://localhost:$APP_PORT"
echo "  - Red local:    http://$LAN_IP:$APP_PORT   (visible para TODOS en la red)"
echo "  - Las migraciones corren solas al arrancar."
echo "  - Revisa los logs con: docker logs -f snip-app"
echo "  - Para datos demo (opcional, UNA vez):"
echo "      docker exec snip-app php artisan db:seed --force"
echo ""
echo "  Detener:   docker stop snip-app snip-db"
echo "  Borrar:    docker rm -f snip-app snip-db && docker network rm $NETWORK"