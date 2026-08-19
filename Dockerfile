FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY public public
RUN npm run build

FROM composer:2 AS vendor
WORKDIR /app
COPY . .
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader

FROM php:8.3-fpm-alpine AS runtime

RUN apk add --no-cache \
    nginx \
    supervisor \
    sqlite \
    icu \
    oniguruma \
    libzip \
    && apk add --no-cache --virtual .build-deps \
    sqlite-dev \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_sqlite \
    intl \
    mbstring \
    zip \
    opcache \
    && apk del .build-deps

WORKDIR /var/www/html

COPY --from=vendor /app/vendor vendor
COPY --from=frontend /app/public/build public/build
COPY . .

COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN php artisan package:discover --ansi \
    && touch database/database.sqlite \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

VOLUME ["/var/www/html/database", "/var/www/html/storage/app"]

EXPOSE 8080

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
