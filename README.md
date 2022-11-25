# Portail des assos - API

[![Build Status](https://travis-ci.org/simde-utc/portail.svg?branch=master)](https://travis-ci.org/simde-utc/portail)
[![GitHub license](https://img.shields.io/github/license/simde-utc/portail.svg)](https://github.com/simde-utc/portail/blob/develop/LICENSE)
[![Api version](https://img.shields.io/badge/version%20api-v1-blue.svg)](https://assos.utc.fr/api/v1)

New API of [Portail des Assos](https://assos.utc.fr), built with [Laravel 5.8](https://laravel.com/) and needs at least PHP 7.1.3

## Quick start with docker

If you already have docker and docker-compose installed, you can quickly setup a dev environment with the following commands:

```bash
docker/compose build
docker/compose up
docker/back php artisan migrate
```

In order to make the app usable and to quickly run common use cases, you can also seed the database with fake (generated) data, such as fake users, fake semesters, fake associations...

```bash
docker/back php artisan db:seed
```

## Documentation

Documentation can be found at [https://simde.gitlab.utc.fr/documentation/#/portail/dev/](https://simde.gitlab.utc.fr/documentation/#/portail/dev/). It is currently incomplete, PRs are welcolme!

## Installation 

See installation documentation [here](https://simde.gitlab.utc.fr/documentation/#/portail/dev/installation) or in `documentation/installation.md`.
