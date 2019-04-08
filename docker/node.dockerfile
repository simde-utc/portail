FROM node:carbon-jessie-slim
MAINTAINER Cesar Richard <cesar.richard2@gmail.com>
WORKDIR /var/www/html

COPY package*.json ./
RUN npm install
RUN npm install --global cross-env
COPY . ./

ENTRYPOINT ["npm","run","watch"]
