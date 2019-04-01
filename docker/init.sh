#!/bin/bash
docker-compose up -d
echo "Installing dependencies"
#docker-compose exec app composer install
echo "Migrations"
docker-compose exec app php artisan migrate:refresh --seed --force
echo "Clearing install"
docker-compose exec app php artisan portail:clear
echo "Stopping containers"
docker-compose stop
