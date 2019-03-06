FROM php:7.1.3-fpm
MAINTAINER Cesar Richard <cesar.richard2@gmail.com>

RUN apt-get update && apt-get install --no-install-recommends -y libmcrypt-dev gnupg git unzip \
    mysql-client libmagickwand-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install mcrypt pdo_mysql gd zip

COPY docker/.env.docker /var/www/html/.env
COPY composer.json /var/www/html
RUN php -r "readfile('https://getcomposer.org/installer');" | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer install --no-autoloader --no-scripts

COPY . /var/www/html
