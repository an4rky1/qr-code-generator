#!/bin/bash

set -e

mkdir -p /var/www/html/storage/framework/{cache,sessions,views,testing}
mkdir -p /var/www/html/storage/logs

if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = " " ]; then
    php artisan key:generate --force 2>/dev/null || true
fi

php artisan migrate --force 2>/dev/null || true
php artisan storage:link --force 2>/dev/null || true

sed -i "s/\${PORT}/${PORT:-8080}/g" /etc/nginx/sites-enabled/default

mkdir -p /var/run/php
chown www-data:www-data /var/run/php

POOL_CONF='[www]
user = www-data
group = www-data
listen = /var/run/php/php-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
pm = dynamic
pm.max_children = 10
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 5
pm.max_requests = 500
clear_env = no
catch_workers_output = yes'

for dir in /etc/php/*/fpm/pool.d /etc/php/*/pool.d /usr/local/etc/php-fpm.d; do
    mkdir -p "$dir" 2>/dev/null || true
    echo "$POOL_CONF" > "$dir/www.conf" 2>/dev/null || true
done

php-fpm -D

for i in $(seq 1 10); do
    if [ -S /var/run/php/php-fpm.sock ]; then
        break
    fi
    sleep 0.5
done

nginx -g "daemon off;"
