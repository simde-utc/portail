# Documentation de l'API

Une documentation automatique de l'API a été mise en place grâce au package [laravel-apidoc-generator](https://github.com/mpociot/laravel-apidoc-generator).
Elle doit ne pas être suivie par git, et donc être générée à chaque fois.


## Accéder à la documentation

La documentation est disponible dans le répertoire `/public/docs/` ; on peut y accéder directement via le navigateur à l'addresse https://assos.utc.fr/docs/ .
Un fichier `collection.json` est également disponible pour importer les routes dans postman ou insomnia.

## Documenter le code

Il est important de documenter le code de la façon suivante pour les Controllers :
```php
<?php

namespace App\Http\Controllers;

/**
 * @resource <Nom De La Ressource>
 *
 * <Gestion de ... Description de la ressource>
 */
class RessourceController extends Controller
{
    /**
     * List|Create|Update|Show|Delete <Ressource>
     *
     * <Description de la fonction>
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        // ...
    }

}
```


## Générer la documentation

Veillez à avoir installé tous les paquets nécessaires via `composer update`.
Ensuite, il faut demander à artisan de générer la documentation :
```
php artisan api:generate --routePrefix="api/*"
```
Il faut compléter le préfixe de route en fonction de la documentation que l'on souhaite générer (ex : api/* pour toutes les routes, api/v1/* pour toutes les routes de la v1 de l'API).


## Mettre la documentation à jour

La commande pour mettre à jour la documentation est :
```
php artisan api:update
```