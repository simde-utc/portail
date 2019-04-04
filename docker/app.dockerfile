FROM php:7.2-fpm
MAINTAINER Cesar Richard <cesar.richard2@gmail.com>

RUN apt-get update && \
    apt-get install --no-install-recommends -y \
    libmcrypt-dev \
    gnupg \
    git \
    unzip \
    mysql-client \
    libmagickwand-dev  \
    zlib1g-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libicu-dev \
    libfontconfig1 \
    libxrender1 \
    libxml2 \
    libxml2-dev \
    && docker-php-source extract \
    && docker-php-ext-configure gd \
        --enable-gd-native-ttf \
        --with-freetype-dir=/usr/include/freetype2 \
        --with-png-dir=/usr/include \
        --with-jpeg-dir=/usr/include \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql zip gd \
    && docker-php-source delete \
RUN pecl channel-update pecl.php.net \
  && pecl install \
     imagick \
     mcrypt

COPY composer.json /var/www/html
RUN php -r "readfile('https://getcomposer.org/installer');" | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer install --no-autoloader --no-scripts

COPY docker/.env.docker .env
