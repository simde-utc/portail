# Portail des assos - API

[![Build Status](https://travis-ci.org/simde-utc/portail.svg?branch=master)](https://travis-ci.org/simde-utc/portail)
[![GitHub license](https://img.shields.io/github/license/simde-utc/portail.svg)](https://github.com/simde-utc/portail/blob/develop/LICENSE)
[![Api version](https://img.shields.io/badge/version%20api-v1-blue.svg)](https://assos.utc.fr/api/v1)

New API of [Portail des Assos](https://assos.utc.fr), built with [Laravel 5.6](https://laravel.com/) and needs at least PHP 7.1.3



## Installation

- Check your PHP version (must be more than 7.1.3) :  `php -v`
- Install [composer](https://getcomposer.org/download/)
- Install `redis` and launch the service (necessary for cache/queue )

- Copy `.env.example` to `.env` :
    + Specify current intallation status prod/dev and debug or not
    + Specify database connection crendentials
    + Specify redis credentials (default: no password)
    + Optional: Specify email and notifications crendentials (for queues)
- Create the database: `portail`
- Install packages with `composer install` (Make sure you are at the project's root folder)

- App installation and server preparation: `php artisan portail:install`
- OU Lancer les commances suivantes :
    + Clear cache : `php artisan portail:clear`
    + Key generation : `php artisan key:generate`
    + Tables creation and seeding : `php artisan migrate:fresh --seed`
	+ JS dependencies installation : `npm install --production` (assez long)
	+ front-end application compilation : `npm run prod` (assez long)

- Application run :
    + Artisan : `php artisan serve` and hit http://localhost:8000 with your web browser
    + Wamp/Apache : Hit the folder `public` of the installation with Wamp with your web browser
- If email and notification crendentials are filled in:
    + Run `php artisan queue:work` for working notification sending.
    + To have a queue system that works independently:
    - Install the `supervisor` package under Linux.
      - Copy and adapt the file `laravel-worker.conf` in `/etc/supervisor/conf.d`
      - launch the worker: `sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start laravel-worker:*`

## Update

- Run `php artisan portail:update`
- Run `npm run prod` or `npm run watch`
- 
## Developement

### Run in dev mode

- Run `npm install` (long)
- Run `npm run watch`
- Enjoy

### Developing

- We use the workflow `Gitflow`, presented in this article [article](https://nvie.com/files/Git-branching-model.pdf). There is an exception: We don't use the `release` branch. This implies that all Pull Requests are merged in `develop`. Once this code is tested it is released version by version on `master`.
- Branch naming:
  - `feature/<issue shortname>` for enhancement.
  - `fix/<issue shortname>` for bug fixes.
  - `hot/<issue shortname>` for hotfixes.
- Follow the linter for PHP and JS
- Comments, Commits and Pull Request must be in 

### Issues

- Open as much issues as possible
- Use tags (labels), in order to precise wether the issue is a bug or a feature request and its importance (in terms of severity or work). You may also indicate the affected area of the code (notifications, frontend, database... etc). You can create new tags if (maintainers will remove redundant tags). Please do not remove tags.

### Contributing

- Urgent issues first (they are tagged as urgent)
- Submit your own issues before working
- PRs must be reviewed by at least one SiMDE developer, except for minor fixes with absolutely no side effect (i.e. comments, local variable names etc.).
- You can join the SiMDE association. We have a [slack](https://simde.slack.com) and we organise meetings during which devs can work together and explain the code. We will be very glad to answer your questions!

## Documentation

Documentation can be found in `documentation/README.md`. It is currently incomplete, PRs are welcolme!
