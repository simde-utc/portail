FROM php:7.1.3-fpm
MAINTAINER Cesar Richard <cesar.richard2@gmail.com>

RUN echo "deb [check-valid-until=no] http://archive.debian.org/debian jessie-backports main" > /etc/apt/sources.list.d/jessie-backports.list
RUN sed -i '/deb http:\/\/deb.debian.org\/debian jessie-updates main/d' /etc/apt/sources.list

RUN apt-get -o Acquire::Check-Valid-Until=false update && apt-get install --no-install-recommends -y libmcrypt-dev gnupg git unzip \
    mysql-client libmagickwand-dev libfreetype6-dev libwebp-dev libjpeg62-turbo-dev libpng-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-configure gd \
                --enable-gd-native-ttf \
                --with-freetype-dir=/usr/include/freetype2 \
                --with-png-dir=/usr/include \
                --with-jpeg-dir=/usr/include \
    && docker-php-ext-install mcrypt pdo_mysql gd zip

COPY composer.json /var/www/html
RUN php -r "readfile('https://getcomposer.org/installer');" | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer install --no-autoloader --no-scripts

COPY . /var/www/html
