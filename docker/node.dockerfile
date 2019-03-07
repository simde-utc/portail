FROM node:carbon-jessie-slim
MAINTAINER Cesar Richard <cesar.richard2@gmail.com>

COPY package*.json ./

RUN npm install --production
RUN npm install --global cross-env
COPY . .
ENTRYPOINT ["npm","run","prod"]
