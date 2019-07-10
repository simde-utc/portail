# Authentification

The authentification system is a modular one.
Each authentification type is described in `config/auth.php`, in the array `services`.


## System Declaration

Each authentification system is declared in `config/auth.php => services` this way:

```php
'services' => [
	'nom_du_système' => [
		'class' => App\Services\Auth\ClasseDuService::class,
		'model' => App\Models\AuthModele::class,
	],
	// ...
],
```

`class` corresponds to the authentification service and `model` to the model.


## LoginController

Located in `app/Http/Controllers/Auth/LoginController.php`, it manages basic login and logout routes.



## Parent authentification service

Located in `app/Services/Auth/AuthService.php`, it is an abstract class that each authentication service must implements.

`abstract` methods must be inherited and implemented by the child service. They are described in the following section.
Ses méthodes sont :
- `public function logout(Request $request)` disconnects a user. 

## Specific authentification service

It must inherits from the `App\Services\Auth\AuthService` parent authentification service.

Methods and attributes that it must inherits and that must be implemented are:
- `public function showLoginForm()` : Returns the link to the login form.
- `abstract function login(Request $request)` : connects the user from the request data (query, input, ...).

The other `AuthService` methods can also be overrided. Especially logout to disconnect a user.

## Table and Model

In `app/Models/Auth`.

Must contain the necessary data for the connection trough the specific system, a foreign key the the linked user and a `last_login_at` timestamp.