ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-cli-bookworm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        libcurl4-openssl-dev \
        libonig-dev \
        libsqlite3-dev \
        sqlite3 \
        unzip \
    ; \
    docker-php-ext-install -j"$(nproc)" \
        curl \
        mbstring \
        pdo_sqlite \
    ; \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/conf.d/*.ini /usr/local/etc/php/conf.d/

WORKDIR /app

COPY app/composer.json app/composer.lock* ./app/
RUN cd app \
    && composer install --no-dev --no-interaction --no-progress --no-scripts --prefer-dist \
    && composer check-platform-reqs --no-dev

COPY . .

RUN mkdir -p database app/runtime/export app/runtime/tmp \
    && chown -R www-data:www-data database app/runtime
