FROM php:7.3.0-apache
MAINTAINER You <you@your.email>

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get update && apt-get install -y git libzip-dev unzip \
    && docker-php-ext-install zip \
    && a2enmod rewrite headers

COPY . /var/www/html

WORKDIR /var/www/html/app

#RUN composer install
