# Portail des assos - API

Nouvelle API du [Portail des Assos](https://assos.utc.fr), construite avec [Laravel 5.6](https://laravel.com/) nécessitant au moins PHP 7.1.3



## Installation

- Vérifier que qu'une version supérieure à 7.1.3 de PHP est installé : `php -v`
- Installer [composer](https://getcomposer.org/download/)
- Installer `redis` et lancer le service (nécessaire pour le fonctionnement du cache/queue)

- Copier `.env.example` en `.env` :
    + Spécifier si l'installation est en prod/dev et si on est en debug
    + Spécifier les identifiants de connexions à la base de données (par exemple localhost)
    + Spécifier les identifiants redis (par défaut redis n'a pas de mdp)
    + Spécifier les CAS_URL et CAS_IMAGE
    + Spécifier, facultativement, les identifiants mail et notifications (pour que les queues marchent)
- Créer la base de données `portail` à la mano
- Installer les packages avec `composer install` (attention à être dans le bon dossier)

- Lancer l'installation et la préparation du serveur: `php artisan quick:install`
- OU Lancer les commances suivantes :
    + Suppression du cache : `php artisan quick:clear`
    + Création de la clé : `php artisan key:generate`
    + Création des tables et des données : `php artisan migrate:fresh --seed`
	+ Installation des dépendances JS : `npm install` (très long)
	+ Compilation de l'application frontend : `npm run dev` (assez long)

- Lancer l'application via :
    + Artisan : `php artisan serve` et aller sur http://localhost:8000
    + Wamp/Apache : aller directement sur le dossier `public` de l'installation via Wamp
- Si les identifiants mail et notification sont renseignés :
    + Lancer `php artisan queue:work` pour avoir l'envoie de notif qui marche
- Ça part !



## Documentation

La documentation se trouve dans `documentation/README.md`
