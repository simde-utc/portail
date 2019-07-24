# API documentation

An automatic API documentation was implemented with the [laravel-apidoc-generator](https://github.com/mpociot/laravel-apidoc-generator) package.
The documentation is not to be tracked by git and must be generated each time.

## Access the documentation

The documentation is available in the folder `/public/docs/` and can be accessed directly from a browser with the adress: [https://assos.utc.fr/docs/](https://assos.utc.fr/docs/).
A file `collection.json` is also available to import routes in postman or insomnia.

## Documenter le code

It is important to document your code the following way for the controllers:

```php
<?php

namespace App\Http\Controllers;

/**
 * @resource <Ressource Name>
 *
 * <Manages ... Ressource descrption>
 */
class RessourceController extends Controller
{
    /**
     * List|Create|Update|Show|Delete <Ressource>
     *
     * <Function decription>
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        // ...
    }

}
```


## Generate documentation

make sure you've installed all necessary packages through `composer update`.
Then let artisan generate the documentation :
```
php artisan api:generate --routePrefix="api/*"
```
The `routePrefix` must be changed depending on the wanted generated documentation (ex : `api/*` for all routes, `api/v1/*` for all the API's V1 version routes).

## Update documentation

The command to update the documentation is:
```
php artisan api:update
```