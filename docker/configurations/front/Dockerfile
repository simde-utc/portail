FROM node:lts
LABEL maintainer="Cesar Richard <cesar.richard2@gmail.com>"

RUN npm install --global cross-env

# Run as non root for safety and to avoid permissions problems

ARG USER_ID
ARG GROUP_ID

RUN userdel -f node &&\
    if getent group node ; then groupdel node; fi &&\
    groupadd -g ${GROUP_ID} developper &&\
    useradd -l -u ${USER_ID} -g developper developper &&\
    mkdir -p /home/developper && chown developper:developper /home/developper &&\
    mkdir -p /app/node_modules && chown -R developper:developper /app

USER developper

WORKDIR /var/www/html

COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["npm", "run", "watch"]
