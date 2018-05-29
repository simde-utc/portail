# Authentification

Le système d'authentification est modulaire.
Chaque type d'authentification est décrit dans `config/auth.php`, dans le tableau `services`.


## Déclaration du système

Chaque système d'authentification est déclaré dans `config/auth.php => services` de la façon suivante :
```php
'services' => [
	'nom_du_système' => [
		'class' => App\Services\Auth\ClasseDuService::class,
		'model' => App\Models\AuthModele::class,
	],
	// ...
],
```

`class` correspond au service d'authentification et `model` au modèle.


## LoginController

Situé dans `app/Http/Controllers/Auth/LoginController.php`, il permet de gérer les routes de base de dé/connexion.



## Service d'authentification parent

Situé dans `app/Services/Auth/AuthService.php`, il s'agit d'une classe abstraite dont doivent hériter chaque service d'authentification.

Les méthodes `abstract` doivent être héritées et modifiées par le service fils, elles sont décrites dans la section suivante.
Ses méthodes sont :
- `public function logout(Request $request)` permet de 


## Service d'authentification spécifique

Il doit hériter du service d'authentification parent `App\Services\Auth\AuthService`.

Les méthodes et attributs qu'il faut hériter et ré-implémenter sont :
- `public function showLoginForm()` : envoie le lien du formulaire de login
- `abstract function login(Request $request)` : login l'utilisateur à partir des informations de requêtes (query, input...)

Les autres méthodes de `AuthService` peuvent aussi être ré-implémentée, notamment `logout()` pour déconnecter l'utilisateur sur l'API externe aussi.




## Table et Model

Dans `app/Models/Auth`

Doit contenir les informations nécessaires à la connexion via le système spécifique, une clé étrangère vers l'utilisateur lié et un timestamp `last_login_at`
