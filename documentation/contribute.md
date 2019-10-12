# How to contribute

Urgent issues first (they are tagged as urgent)

Submit your own issues before working

PRs must be reviewed by at least one SiMDE developer, except for minor fixes with absolutely no side effect (i.e. comments, local variable names etc.).

You can join the SiMDE association. We have a [slack](https://simde.slack.com) and we organise meetings during which devs can work together and explain the code. We will be very glad to answer your questions!

## Table of content
- [How to contribute](#how-to-contribute)
  - [Table of content](#table-of-content)
  - [Developing](#developing)
  - [Issues](#issues)
  - [Run portal in dev mode](#run-portal-in-dev-mode)
  - [Update](#update)
  - [Commands](#commands)

## Developing

We use the workflow `Gitflow`, presented in this article [article](https://nvie.com/files/Git-branching-model.pdf).

There is an exception: We don't use the `release` branch. 

This implies that all Pull Requests are merged in `develop`.

Once this code is tested it is released version by version on `master`.

- Branch naming:
  - `feature/<issue shortname>` for enhancement.
  - `fix/<issue shortname>` for bug fixes.
  - `hot/<issue shortname>` for hotfixes.
  
- Follow the linter for PHP and JS

- Comments, Commits and Pull Request must be in English

## Issues

Open as much issues as possible

Use tags (labels), in order to precise wether the issue is a bug or a feature request and its importance (in terms of severity or work). You may also indicate the affected area of the code (notifications, frontend, database... etc). You can create new tags if (maintainers will remove redundant tags). Please do not remove tags.

## Run portal in dev mode
On two terminals:
- Run `php artisan serve`
- Run `npm run watch`
- Enjoy and help us!

## Update

- Run `php artisan portail:update`
- Run `npm run prod` or `npm run watch`

## Commands

Some `artisan` command were developed to simplify the application installation an maintenance.
- `php artisan portail:install` to install or update the whole application.
- `php artisan portail:clear` to delete all cached data.
- `php artisan portail:optimize` to cache resources.


