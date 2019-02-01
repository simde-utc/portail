# Installation et Mise à jour

- [Installation](#installation)
- [Mise à jour](#mise-à-jour)
- [En cas de problèmes](#en-cas-de-problèmes)
- [Commandes](#commandes)

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


## Mise à jour

- Mettre à jour les packages php avec `composer update`
- Mettre à jour les packages npm avec `npm install`
- Relancer les migrations avec `php artisan migrate:fresh --seed`


## En cas de problèmes

- Dans `php.ini` augmenter `memory_limit` à au moins 4G
- `composer dump-autoload`
- `php config:clear`


## Commandes

Des commandes artisan ont été développées pour simplifier l'installation et la maintenance de l'application :
- `php artisan quick:install` pour installer ou mettre à jour toute l'application
- `php artisan quick:clear` pour supprimer les ressources mise en cache
- `php artisan quick:optimize` pour mettre en cache les ressources
