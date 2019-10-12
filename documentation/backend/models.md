# Models

They are data models, with wich it is possible to interract trough Eloquent. 

Namespace : `\App\Models\...` <br>
Folder :   `app/Models`

## Table of content

- [Models](#models)
  - [Table of content](#table-of-content)
  - [User](#user)
  - [UserPreferences](#userpreferences)
  - [AuthCas](#authcas)
  - [AuthPassword](#authpassword)

## User
```
id: int() primary key
email: varchar(128) unique
lastname: varchar(128) nullable
firstname: varchar(128) nullable
last_login_at: timestamp
```

Laravel manages the token, create timestamps and update timestamps itself.


## UserPreferences
```
user_id: int() fk -> user.id
email: varchar(128) unique nullable
```
Laravel manages create and update timestamps itself.


## AuthCas
```
user_id: int() fk -> user.id
login: varchar(16) unique
email: varchar(128) unique nullable
active: boolean() default(1)
last_login_at: timestamp
```

The `active` boolean indicates if the CAS connexion is still possible for the user.
Laravel manages create and update timestamps itself.


## AuthPassword
```
user_id: int() fk -> user.id
password: varchar(512) unique
last_login_at: timestamp
```

Laravel manages create and update timestamps itself.