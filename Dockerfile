FROM serversideup/php:8.3-fpm-nginx AS base

ENV AUTORUN_ENABLED=false
ENV SSL_MODE=off

RUN install-php-extensions gd exif

FROM base AS vendor

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

FROM base AS assets

COPY package.json package-lock.json vite.config.js ./
RUN npm ci && npm run build

FROM base

COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
COPY --from=assets /var/www/html/public/build /var/www/html/public/build

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache \
    && touch /var/www/html/database/database.sqlite \
    && chown www-data:www-data /var/www/html/database/database.sqlite
