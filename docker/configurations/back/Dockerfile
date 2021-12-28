FROM php:7.2-fpm
LABEL maintainer="Cesar Richard <cesar.richard2@gmail.com>"

RUN apt-get update
RUN apt-get install --no-install-recommends -y \
    libmcrypt-dev \
    gnupg \
    git \
    unzip \
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
    libpng-dev \
    g++
RUN docker-php-source extract
RUN docker-php-ext-configure intl
RUN docker-php-ext-configure zip
RUN docker-php-ext-configure gd \
            --with-freetype-dir=/usr/include/ \
            --with-jpeg-dir=/usr/include/ \
            --with-png-dir=/usr/include/
RUN docker-php-ext-install mysqli pdo_mysql zip gd intl
RUN docker-php-source delete \
    && pecl channel-update pecl.php.net \
    && pecl install \
     imagick \
     mcrypt \
    && php -r "readfile('https://getcomposer.org/installer');" | php \
    && mv composer.phar /usr/local/bin/composer

ENV PHP_EXTRA_CONFIGURE_ARGS --enable-fpm # --with-fpm-user=developper --with-fpm-group=developper

# Run as non root for safety and to avoid permissions problems
#ARG USER_ID
#ARG GROUP_ID
#
#RUN groupadd -g ${GROUP_ID} developper &&\
#    useradd -l -u ${USER_ID} -g developper developper &&\
#    mkdir -p /home/developper && chown developper:developper /home/developper &&\
#    mkdir /var/www/html/vendor && chown developper:developper /var/www/html/vendor
#USER developper

WORKDIR /var/www/html

# Let the user install dependencies

COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
