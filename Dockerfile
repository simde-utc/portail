FROM php:7.1.3-apache
# install
RUN apt-get update && apt-get install -y \
    git \
    zlib1g-dev \
    libjpeg62-turbo-dev \
    wget gnupg \
  && docker-php-source extract \
  && docker-php-ext-install mysqli pdo_mysql zip gd \
  && docker-php-source delete

# Add VirtualHost for API
RUN { \
    echo '<VirtualHost *:81>'; \
    echo 'ServerAdmin webmaster@localhost'; \
    echo 'DocumentRoot /var/www/html/api/v2/public'; \
    echo 'ErrorLog ${APACHE_LOG_DIR}/error.log'; \
    echo 'CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
    echo '</VirtualHost>'; \
  } | tee "$APACHE_CONFDIR/sites-available/001-api.conf" \
  && a2ensite 001-api \
  && echo 'Listen 81' >> "$APACHE_CONFDIR/ports.conf"
EXPOSE 81

# PHP config
COPY docker/php.ini /usr/local/etc/php/
COPY docker/xdebug.ini /usr/local/etc/php/conf.d/
COPY docker/dev.env /var/www/html/.env

# Composer
COPY composer.json /var/www/html
RUN php -r "readfile('https://getcomposer.org/installer');" | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer install --no-autoloader --no-scripts

# Add code
COPY . /var/www/html

# Launch composer for autoloader and scripts
RUN composer install

RUN php artisan portail:install \
&& php artisan portail:clear \
&& php artisan key:generate \
&& php artisan migrate:fresh --seed \
&& npm install --production \
&& npm run prod

RUN chmod +x /var/www/html/docker/entrypoint.sh
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]

CMD ["apache2-foreground"]
