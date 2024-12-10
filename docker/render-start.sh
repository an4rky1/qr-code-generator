#!/bin/bash

set -e

mkdir -p /var/www/html/storage/framework/{cache,sessions,views,testing}
mkdir -p /var/www/html/storage/logs

php artisan storage:link --force 2>/dev/null || true

sed -i "s/\${PORT}/${PORT:-8080}/g" /etc/nginx/sites-enabled/default

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

nginx -g "daemon off;"
