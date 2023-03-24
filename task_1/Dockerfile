FROM php:latest

RUN apt-get update && apt install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /test_assignment_vl

COPY ./ /test_assignment_vl

RUN composer install
