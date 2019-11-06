# User Model

## Brute table

```
id: int() primary key
email: varchar(128) unique
firstname: varchar(128) nullable
lastname: varchar(128) nullable
image:  varchar(255) nullable
is_active tinyint(1) default 1
last_login_at: timestamp
```

The `image` field is an external link to the image.

Laravel manages a the remember token (`remember_token`), create timestamps (created_at and updated_at) and update timestamps itself. Documentation about these functionnality can be found [here (remember token)](https://laravel.com/docs/5.8/authentication#remembering-users) and [here (timestamps)](https://laravel.com/docs/5.8/eloquent#eloquent-model-conventions).

## Methods
### getMeAttribute()
#### Return values
This method returns a User model instance of the current authenticated user.

### getNameAttribute()
#### Return values
Return the result of the concatenation of the user's firstname and lastname (in UPPERCASE) separated by a space.
Ex: `Jean DUPONT` 

### getAuthPassword()
#### Return Values
Return the crypted password of the user. (string)

### findByEmail()
This method gets a user with his email adress.
#### Parameters
String email: email adress
#### Return values
Return a `User` instance of the requested user or nothing if the user has not been found.

### getUsers()
TODO

### notificationChannels()
TODO

### getNotificationForMail()
TODO

### notifyOnEdition()
This method notifies the user on edition of email adress, lastname, firstname or profile picture.
#### Return values
- `true` if the user has been notified
- `false` otherwise
- 
### getTypes()
#### Return values
Return all possible user types.

Return value on october 2019:
```php
[
    'admin',
    'member',
    'contributorBde',
    'casConfirmed',
    'cas',
    'password',
    'active',
]
```


### getTypeDescriptions()
#### Return Values
Return the all possible user types and their description.

Return value on october 2019:
```php
[
    'admin' => 'administrateur',
    'member' => 'membre d\'une association',
    'contributorBde' => 'cotisant BDE',
    'casConfirmed' => 'membre UTC/ESCOM',
    'cas' => 'avec connexion CAS',
    'password' => 'avec connexion email/mot de passe',
    'active' => 'compte actif',
]
```
### isType()
#### Parameters
String value of a type.
Possible values:
- active
- admin
- cas
- casConfirmed
- contributorBde
- member
- password
- app

#### Return values
- `true` if user if of requested type.
- `false` otherwise.

### type()
#### Return Values
String of the main type of the user. 
Possible Values:
- active
- admin
- cas
- casConfirmed
- contributorBde
- member
- password
- app

### isPublic()
Indicates if a user is a public user. All users are currently public.
#### Return Value
- Always `true`

### isActive()
#### Return Values
TO DO


### isCas()
#### Return Values
TO DO

### isCasConfirmed()
TO DO

### isPassword()
#### Return Values
- `true` if the user can log in the portal with a password. 
- `false` for other means of authentication.

### isApp()
#### Return Values
- `true` if the user can log in the portal with a password. 
- `false` for other means of authentication.

### isContributorBde()
#### Return Values

- `true` if the user is a contributor of the BDE-UTC or if the user is the website administrator in `debug` mode (`APP_DEBUG` field in `.env`). The last case has been implemented to allow developpers to be BDE contributors on local instances without any ginger key.
- `false` if the user is not contributor at the BDE-UTC
- `null` if the BDE-UTC contributor request fails (invalid or non-existent env `GINGER_KEY`) and then the app is not in debug mode or the user is not the portal's administrator.

### isMember()
#### Return Values

- `true` if the user joined at least one association during **this** semester - `false` otherwise


### isAdmin()
#### Return Values

Return wether or not the user is an administrator of the portal.


### getLang()
#### Return Values

Return the ISO 639-1 lang code of the user. Default is french (`fr`).