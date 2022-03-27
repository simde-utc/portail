# Installation

The pre-install contains the installation of the project's stack and tools.

The install section describes the installation for this particulary project.
## Table of content
- [Installation](#installation)
	- [Table of content](#table-of-content)
	- [Pre-install](#pre-install)
		- [Install PHP 7, mysql, Redis, Git, Composer and npm](#install-php-7-mysql-redis-git-composer-and-npm)
		- [Choose right version of node and npm](#choose-right-version-of-node-and-npm)
		- [Create MySQL User and Database](#create-mysql-user-and-database)
	- [Install](#install)
		- [Portal](#portal)
	- [For email and notification credentials](#for-email-and-notification-credentials)
	- [For BDE contributions management](#for-bde-contributions-management)
	- [Credentials](#credentials)
	- [In case of a problem](#in-case-of-a-problem)

## Pre-install

### Install PHP 7, mysql, Redis, Git, Composer and npm

Install made for Ubuntu, Debian and Mint.

Required version is `>=7.2` it is forced in `Project-root/composer.json`. This indicates how to install php version 7.2.

```bash
sudo apt update
sudo apt upgrade -y
sudo apt install -y \
	php7.2 php7.2-mbstring php7.2-dg php7.2-dom php7.2-mysql \
	redis \
	git \
	composer \
	npm \
	mysql-server
```

### Choose right version of node and npm

```bash
nvm install lts/dubnium
npm i -g npm
```
### Create MySQL User and Database

Enter into MySQL as root:
```bash
sudo mysql
```
Then, inside create a MySQL user and a database using the following commands:
```bash
GRANT ALL PRIVILEGES ON portail.* TO 'portail'@'localhost' IDENTIFIED BY 'password'; # User creation with all privileges on all tables of the `portail` database
CREATE DATABASE portail; # Database creation
\q #Quit
```

## Install

### Portal

```bash
git clone https://github.com/simde-utc/portail.git
cd portail
cp .env.example .env
```
Here you have to fill the `.env` file.

If you used the given commands, there's no need to change `DB_*` values. Otherwise, this is the basic mysql configuration. If you have any problem, ask someone of the team.

Fill `ADMIN_EMAIL`, `ADMIN_FIRSTNAME`, `ADMIN_LASTNAME` with your own information.

Finally launch this last command. It will ask you some questions:
- `/!\ A .env file already exists /!\`  `Do you want to replace it (old version will be placed in .env.last ? yes/no [no]:` : Type enter.
- `Clear database ? (yes/no) [no]` : answer yes
- `Seed database ? (yes/no) [no]` : answer yes
```bash
composer install
php artisan portail:install
```

OR run the following commands :
```bash
php artisan portail:clear # Clear cache
php artisan key:generate  # Key generation
php artisan migrate:fresh --seed # Tables creation and seeding
npm install --production # JS dependencies installation (pretty slow)
npm run prod # Front-end application compilation (dammit JS so slooow)
```

## For email and notification credentials
You only need to install this if you want to work on emails and notification

```bash
php artisan queue:work # For working notification sending.
```
To have a queue system that works independently:
```bash
sudo apt install -y supervisor
cp laravel-worker.conf /etc/supervisor/conf.d # Adapt this file
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start laravel-worker:*
```

## For BDE contributions management
You only need to install this if you want to work on BDE contributions.
- Install [Fake ginger](https://github.com/simde-utc/faux-ginger) and follow the installation guide.
- Make sure you have it running and specify the `GINGER_KEY` and `GINGER_URL` values.
- Run
```bash
php artisan portail:clear
```
- Keep in mind that your actions with fake Ginger are limited, for more advanced purposes, install  [Ginger API](https://github.com/simde-utc/ginger)

## Credentials
If you need to use the api except from the frontend, you'll need to connect with the API through Oauth2 protocol.

- Admin user id: `45617374-6572-2065-6767-7321202b5f2b`
- Oauth client:
	+ id: `53616d79-206a-6520-7427-61696d652021`
	+ secret: `password`
	+ redirect: `http://localhost:8000`
	+ scopes: `*` for all scopes


## In case of a problem

```bash
composer dump-autoload
php artisan portail:clear
```

### MissingTokenError at /oauth/callback

If you have a MissingTokenError, try to downgrade ```lcobucci/jwt``` to 3.3.3 with composer.
```bash 
composer require lcobucci/jwt=3.3.3
```

or directly in ```composer.json```:
```json
"require": {
	...,
    "lcobucci/jwt": "^3.3.3",
	...,
}
```

and then run : 
```bash
composer update
```
