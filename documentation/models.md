# Models

[TOC]

Il s'agit des modèles de données, avec lesquelles on peut intéragir via Eloquent.

Namespace : `\App\Models\...`
<br>Dossier :   `app/Models`


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
