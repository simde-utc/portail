# Portail des assos - API

Nouvelle API du [Portail des Assos](https://assos.utc.fr), construite avec [Laravel 5.6](https://laravel.com/) nécessitant au moins PHP 7.1.3

## Installation

- Vérifier que qu'une version supérieure à 7.1.3 de PHP est installé : `php -v`
- Installer [composer](https://getcomposer.org/download/)
- Installer les packages avec `composer install`
- Renommer `.env.example` en `.env` et spécifier les identifiants de connexions à la base de données
- Lancer les commances suivantes :
    + Suppression du cache : `php artisan config:clear`
    + Création de la clé : `php artisan key:generate`
- Lancer l'application via :
    + Artisan : `php artisan serve` et aller sur http://localhost:8000
    + Wamp : aller directement sur le dossier `public` de l'installation via Wamp
- Ça part !


## Architecture

`app/` : La plupart de l'application se trouve ici
`app/Models` : les modèles de données
`app/Http/Controllers` : traite les requêtes et envoie des réponses
`app/Http/Middleware` : modifie les requêtes avant qu'elles soient traitées
`app/Http/Requests` : valide ou non les données envoyées en POST


## Models

> Dans \App\Models

### User
```
#login: varchar(10)
email: varchar() unique
prenom: varchar()
nom: varchar()
```


## API

