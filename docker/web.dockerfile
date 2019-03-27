FROM nginx:1.10
MAINTAINER Cesar Richard <cesar.richard2@gmail.com>

COPY docker/vhost.conf /etc/nginx/conf.d/default.conf
