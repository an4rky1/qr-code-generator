#!/bin/bash

set -e

mkdir -p /var/www/html/storage/framework/{cache,sessions,views,testing}
mkdir -p /var/www/html/storage/logs

php artisan storage:link --force 2>/dev/null || true

sed -i "s/\${PORT}/${PORT:-8080}/g" /etc/nginx/sites-enabled/default

php-fpm -D

nginx -g "daemon off;"
