FROM php:8.0-cli

RUN apt-get update && apt-get install -y \
    libpq-dev \
    wget \
    zlib1g-dev \
    libmcrypt-dev \
    libzip-dev \
    librabbitmq-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && docker-php-ext-install sockets

RUN docker-php-ext-install pdo pdo_mysql zip

RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/bin --filename=composer --quiet

WORKDIR /symfony
