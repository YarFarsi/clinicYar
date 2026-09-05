FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    git unzip icu-dev libzip-dev oniguruma-dev mysql-client \
    && docker-php-ext-install pdo_mysql mbstring zip intl bcmath opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data
CMD ["php-fpm"]
