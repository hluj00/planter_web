FROM php:7.3.0-apache
MAINTAINER jan hlubucek <jhlubucek@jhlubucek.cz>

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get update && apt-get install -y git libzip-dev unzip \
    && apt-get install -y \
            cron \
    && docker-php-ext-install zip pdo pdo_mysql \
    && a2enmod rewrite headers

RUN echo '*/5 * * * * root cd /var/www/html/app && /usr/local/bin/php bin/console app:create-actions' >>/etc/crontab
RUN echo '55 * * * * root cd /var/www/html/app && /usr/local/bin/php bin/console app:prepare-daily-notifications' >>/etc/crontab
RUN echo '*/5 * * * * root cd /var/www/html/app && /usr/local/bin/php bin/console app:prepare-notifications' >>/etc/crontab
RUN echo '*/2 * * * * root cd /var/www/html/app && /usr/local/bin/php bin/console app:send-notifications' >>/etc/crontab

COPY . /var/www/html

WORKDIR /var/www/html/app

#RUN composer install
COPY ./php.ini /usr/local/etc/php/
RUN chown -R www-data:www-data /var/www/html/
