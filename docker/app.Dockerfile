# WEBPACK BUILD (js)

FROM node:lts AS webpack_build
MAINTAINER Cesar Richard <cesar.richard2@gmail.com>

WORKDIR /tmp

COPY package*.json ./
RUN npm install && npm install --global cross-env

COPY . .
RUN npm run dev \
  && chmod -R 777 storage \
  && rm -rf node_modules

# APP (php)

FROM php:7.2-fpm
LABEL maintainer="Cesar Richard <cesar.richard2@gmail.com>"

WORKDIR /var/www/html

RUN apt-get update && \
    apt-get install --no-install-recommends -y \
    libmcrypt-dev \
    gnupg \
    git \
    unzip \
    # mysql-client \
    libmagickwand-dev  \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libicu-dev \
    libfontconfig1 \
    libxrender1 \
    libxml2 \
    libxml2-dev \
    g++ \
    && docker-php-source extract \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-configure gd \
            --enable-gd-native-ttf \
            --with-freetype-dir=/usr/include/freetype2 \
            --with-png-dir=/usr/include \
            --with-jpeg-dir=/usr/include \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql zip gd intl \
    && docker-php-source delete \
    && pecl channel-update pecl.php.net \
    && pecl install \
     imagick \
     mcrypt \
    && php -r "readfile('https://getcomposer.org/installer');" | php \
    && mv composer.phar /usr/local/bin/composer

COPY --from=webpack_build /tmp .

RUN composer install --no-autoloader --no-scripts

COPY docker/.env.docker .env
COPY docker/setup.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
