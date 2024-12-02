#!/bin/bash

set -e

sed -i "s/\${PORT}/${PORT:-8080}/g" /etc/nginx/sites-enabled/default

php artisan storage:link --force 2>/dev/null || true

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
