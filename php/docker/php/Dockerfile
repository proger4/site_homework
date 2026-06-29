ARG PHP_VERSION=7.4.33
FROM php:${PHP_VERSION}-fpm-bullseye

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        libonig-dev \
        libsqlite3-dev \
        libzip-dev \
        sqlite3 \
        unzip \
    ; \
    docker-php-ext-install -j"$(nproc)" \
        mbstring \
        opcache \
        zip \
    ; \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/conf.d/*.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --prefer-dist \
    && composer check-platform-reqs --no-dev

COPY . .

RUN mkdir -p database runtime public/assets \
    && chown -R www-data:www-data database runtime public/assets
