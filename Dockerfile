FROM serversideup/php:8.4-fpm-nginx AS base

ENV AUTORUN_ENABLED=false
ENV SSL_MODE=off

USER root
RUN install-php-extensions gd exif

FROM base AS vendor

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

FROM node:22-alpine AS assets

WORKDIR /var/www/html
COPY package.json package-lock.json vite.config.js ./
COPY resources/ resources/
RUN npm ci && npm run build

FROM base AS render

COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
COPY --from=assets /var/www/html/public/build /var/www/html/public/build

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache \
    && touch /var/www/html/database/database.sqlite \
    && chown www-data:www-data /var/www/html/database/database.sqlite

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx-render.conf /etc/nginx/sites-enabled/default
COPY docker/render-start.sh /usr/local/bin/render-start.sh
RUN chmod +x /usr/local/bin/render-start.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/render-start.sh"]
