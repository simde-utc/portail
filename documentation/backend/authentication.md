# Authentication

The authentication system is modular.
Each authentication type is described in `config/auth.php`, in the array `services`.

## Table of content
- [Authentication](#authentication)
	- [Table of content](#table-of-content)
	- [System Declaration](#system-declaration)
	- [LoginController](#logincontroller)
	- [Parent authentication service](#parent-authentication-service)
	- [Specific authentication service](#specific-authentication-service)
	- [Table and Model](#table-and-model)

## System Declaration

Each authentication system is declared in `config/auth.php => services` this way:

```php
'services' => [
	'nom_du_système' => [
		'class' => App\Services\Auth\ClasseDuService::class,
		'model' => App\Models\AuthModele::class,
	],
	// ...
],
```

`class` corresponds to the authentication service and `model` to the model.


## LoginController

Located in `app/Http/Controllers/Auth/LoginController.php`, it manages basic login and logout routes.



## Parent authentication service

Located in `app/Services/Auth/AuthService.php`, it is an abstract class that each authentication service must implement.

`abstract` methods must be inherited and implemented by the child service. They are described in the following section.
Ses méthodes sont :
- `public function logout(Request $request)` disconnects a user. 

## Specific authentication service

It must inherit from the `App\Services\Auth\AuthService` parent authentification service.

Methods and attributes that it must inherit and that must be implemented are:
- `public function showLoginForm()` : Returns the link to the login form.
- `abstract function login(Request $request)` : connects the user from the request data (query, input, ...).

The other `AuthService` methods can also be overrided. Especially logout to disconnect a user.

## Table and Model

In `app/Models/Auth`.

Must contain the necessary data for the connection through the specific system, a foreign key the the linked user and a `last_login_at` timestamp.