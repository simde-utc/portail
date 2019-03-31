FROM bitnami/php-fpm
MAINTAINER Cesar Richard <cesar.richard2@gmail.com>

RUN apt-get update
RUN apt-get install --no-install-recommends -y libmcrypt-dev gnupg git unzip \
    mysql-client libmagickwand-dev
RUN pecl channel-update pecl.php.net
RUN pecl install imagick
RUN docker-php-ext-enable imagick
RUN docker-php-ext-install mcrypt pdo_mysql gd zip

COPY composer.json /var/www/html
RUN php -r "readfile('https://getcomposer.org/installer');" | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer install --no-autoloader --no-scripts

COPY . /var/www/html
COPY docker/.env.docker .env
