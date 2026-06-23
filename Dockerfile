FROM php:7.4-cli

RUN apt-get update && apt-get install -y \
        git unzip libzip-dev libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring bcmath zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html