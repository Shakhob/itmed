FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y curl git zip libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

USER "1000:1000"

ENTRYPOINT [ "php-fpm" ]
