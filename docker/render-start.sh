#!/bin/bash

set -e

mkdir -p /var/www/html/storage/framework/{cache,sessions,views,testing}
mkdir -p /var/www/html/storage/logs

php artisan storage:link --force 2>/dev/null || true

sed -i "s/\${PORT}/${PORT:-8080}/g" /etc/nginx/sites-enabled/default

POOL_CONF=$(find /etc/php* -name "www.conf" -path "*/pool.d/*" 2>/dev/null | head -1)
if [ -n "$POOL_CONF" ]; then
    grep -q "^user\s*=" "$POOL_CONF" 2>/dev/null || echo "user = www-data" >> "$POOL_CONF"
    grep -q "^group\s*=" "$POOL_CONF" 2>/dev/null || echo "group = www-data" >> "$POOL_CONF"
    grep -q "^listen\.owner\s*=" "$POOL_CONF" 2>/dev/null || echo "listen.owner = www-data" >> "$POOL_CONF"
    grep -q "^listen\.group\s*=" "$POOL_CONF" 2>/dev/null || echo "listen.group = www-data" >> "$POOL_CONF"
fi

php-fpm -D

nginx -g "daemon off;"
