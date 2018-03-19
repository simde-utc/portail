# Portail des assos - API

Nouvelle API du [Portail des Assos](https://assos.utc.fr), construite avec [Laravel 5.6](https://laravel.com/) nécessitant au moins PHP 7.1.3



## Installation

- Vérifier que qu'une version supérieure à 7.1.3 de PHP est installé : `php -v`
- Installer [composer](https://getcomposer.org/download/)
- Installer les packages avec `composer install` (attention à être dans le bon dossier)
- Copier `.env.example` en `.env` et spécifier les identifiants de connexions à la base de données (par exemple localhost)
- Lancer les commances suivantes :
    + Suppression du cache : `php artisan config:clear`
    + Création de la clé : `php artisan key:generate`
- Créer la base de données `portail` à la mano
- Création des tables et des données : `php artisan migrate:fresh --seed`
- Installation de OAuth2:
 	+ Installation interne: `php artisan passport:install`
	+ Installation des paquets JS: `npm install` (très long)
	+ Installation des dépendances JS: `npm run dev` (assez long)
- Lancer l'application via :
    + Artisan : `php artisan serve` et aller sur http://localhost:8000
    + Wamp/Apache : aller directement sur le dossier `public` de l'installation via Wamp
- Ça part !



## Documentation

La documentation se trouve dans `documentation/README.md`
