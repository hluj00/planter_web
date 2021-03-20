FROM php:7.3.0-apache
MAINTAINER jan hlubucek <jhlubucek@jhlubucek.cz>

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get update && apt-get install -y git libzip-dev unzip \
    && docker-php-ext-install zip pdo pdo_mysql \
    && a2enmod rewrite headers

COPY . /var/www/html

WORKDIR /var/www/html/app

#RUN composer install
COPY ./php.ini /usr/local/etc/php/
RUN chown -R www-data:www-data /var/www/html/
