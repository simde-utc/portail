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



## Models

Il s'agit des modèles de données, avec lesquelles on peut intéragir via Eloquent.
Namespace : `\App\Models\...`
Dossier :   `app/Models`


### User
```
login: varchar(10) primary key
email: varchar() unique
prenom: varchar()
nom: varchar()
```



## Controllers

Interfaces de validation des données envoyées en POST.
Namespace : `\App\Http\Requests\...`
Dossier :   `app/Http/Requests`



## Middlewares

Ils permettent de modifier les requêtes avant qu'elles ne soient traitées.
Namespace : `\App\Http\Middleware\...`
Dossier :   `app/Http/Middleware`



## Services

Il s'agit des services externes tels que le CAS ou Ginger
Namespace : `\App\Services\...`
Dossier :   `app/Services`


### CAS


### Ginger



## API

Voici les routes de l'API
