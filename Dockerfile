# Use the specified PHP image as the base
FROM php:8.2-cli

RUN apt-get update && \
    apt-get install -y git unzip libcurl4-openssl-dev && \
    docker-php-ext-install bcmath curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY composer.json ./

RUN composer install --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /app
