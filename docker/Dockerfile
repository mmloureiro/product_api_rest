# syntax=docker/dockerfile:1.4

# 1. Base image for PHP extensions
FROM php:8.3-fpm-alpine AS app_php

# Install system dependencies
RUN apk add --no-cache \
    acl \
    fcgi \
    file \
    gettext \
    git \
    libzip-dev \
    zlib-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    postgresql-client \
    linux-headers \
    zip

# Install PHP extensions and build dependencies temporarily
RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        autoconf \
        g++ \
        make \
    ; \
    \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j$(nproc) \
        intl \
        pdo_pgsql \
        zip \
        opcache \
        gd \
    ; \
    pecl install apcu; \
    pecl install xdebug-3.4.1; \
    docker-php-ext-enable apcu xdebug; \
    \
    runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )"; \
    apk add --no-cache --virtual .app-phpexts-rundeps $runDeps; \
    \
    apk del .build-deps

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 2. Composer stage (dependencies)
FROM app_php AS app_composer

# Copy only composer files first to leverage Docker cache
COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
    composer install --prefer-dist --no-autoloader --no-scripts --no-progress; \
    composer clear-cache

# 3. Final image
FROM app_php AS app_runtime

# Copy PHP config
COPY docker/php/php.ini $PHP_INI_DIR/conf.d/app.ini
COPY docker/php/xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini

# Copy project files
COPY --link . .

# Copy dependencies from composer stage
COPY --from=app_composer /var/www/html/vendor ./vendor

# Finalize composer (dump-autoload)
RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer dump-autoload --optimize --classmap-authoritative; \
    composer run-script post-install-cmd; \
    chmod -R 777 var/

# Permissions fix using ACL
RUN set -eux; \
    chown -R www-data:www-data /var/www/html; \
    setfacl -R -m u:www-data:rwX -m u:$(whoami):rwX var; \
    setfacl -dR -m u:www-data:rwX -m u:$(whoami):rwX var

USER www-data

EXPOSE 9000
