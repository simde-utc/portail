# Documentation

[TOC]

## Models

Il s'agit des modèles de données, avec lesquelles on peut intéragir via Eloquent.
Namespace : `\App\Models\...`
Dossier :   `app/Models`


### User
```
id: int() primary key
email: varchar(128) unique
lastname: varchar(128) nullable
firstname: varchar(128) nullable
last_login_at: timestamp
```

Laravel gère ensuite automatiquement le token et les timestamps de création et de modification


### UserPreferences
```
user_id: int() fk -> user.id
email: varchar(128) unique nullable
```

Laravel gère ensuite automatiquement les timestamps de création et de modification


### AuthCas
```
user_id: int() fk -> user.id
login: varchar(16) unique
email: varchar(128) unique nullable
active: boolean() default(1)
last_login_at: timestamp
```

Le bool active indique si la connexion CAS est toujours possible pour l'utilisateur
Laravel gère ensuite automatiquement les timestamps de création et de modification


### AuthPassword
```
user_id: int() fk -> user.id
password: varchar(512) unique
last_login_at: timestamp
```

Laravel gère ensuite automatiquement les timestamps de création et de modification


## Permissions7

Avec le package [spatie/laravel-permission](https://github.com/spatie/laravel-permission)

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

Pour créer un nouveau service/système d'authentification, il suffit de créer une classe hérité du service AuthService.php et d'overrider les fonctions de base. Il est aussi nécessaire d'ajouter dans config/auth.php le service.

### CAS


### Ginger



## API

Voici les routes de l'API
