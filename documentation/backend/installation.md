# Installation and Update

- [Installation and Update](#Installation-and-Update)
  - [Installation](#Installation)
  - [Update](#Update)
  - [In case of a problem](#In-case-of-a-problem)
  - [Commands](#Commands)

## Installation

- Vérifier qu'une version supérieure à 7.1.3 de PHP est installé : `php -v`
- Installer composer : https://getcomposer.org/download/
- Installer les packages avec `composer install` (attention à être dans le bon dossier)
- Copier `.env.example` en `.env` et spécifier les identifiants de connexions à la base de données (par exemple localhost)
- Lancer les commances suivantes :
    + Suppression du cache : `php artisan config:clear`
    + Création de la clé : `php artisan key:generate`
- Créer la base de données MySQL `portail` manuellement
- Lancer la commande suivante : `php artisan migrate:fresh`
- Pour populer la BDD : `php artisan db:seed`
- Lancer l'application via :
    + Artisan : `php artisan serve` et aller sur http://localhost:8000
    + Wamp : aller directement sur le dossier `public` de l'installation via Wamp
- Ça part !


## Update

- Update all php packages with `composer update`
- Update all npm packages with `npm install`
- Reload migrations with `php artisan migrate:fresh --seed`


## In case of a problem

- `composer dump-autoload`
- `php config:clear`


## Commands

Some `artisan` command were developed to simplify the application installation an maintenance.
- `php artisan portail:install` to install or update the whole application.
- `php artisan portail:clear` to delete all cached data.
- `php artisan portail:optimize` to cache resources.
