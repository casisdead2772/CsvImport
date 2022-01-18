FROM php:8.0-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    wget \
    zlib1g-dev \
    libmcrypt-dev \
    libzip-dev \
    librabbitmq-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /symfony
