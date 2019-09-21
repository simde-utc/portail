# PROXY (nginx)

FROM nginx:stable

WORKDIR /var/www/html

COPY --from=simde_portal_app:latest /var/www/html .
COPY docker/vhost.conf /etc/nginx/conf.d/default.conf
