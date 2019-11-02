# Scopes

Scopes services

## Table of content

- [Scopes](#scopes)
  - [Table of content](#table-of-content)
  - [Properties](#properties)
  - [allowPublic() method](#allowpublic-method)
  - [getAuthMiddleware() method](#getauthmiddleware-method)
  - [all() method](#all-method)
  - [getAllByCategories() method](#getallbycategories-method)
  - [nextVerbs() method](#nextverbs-method)
    - [Parameters](#parameters)
  - [find() method](#find-method)
    - [Parameters](#parameters-1)
    - [Return value](#return-value)
    - [Exceptions](#exceptions)
  - [get() method](#get-method)
  - [getByCategories() method](#getbycategories-method)
  - [getDevScopes() method](#getdevscopes-method)

## Properties

- `scopes` (protected): Contains an array of all scopes in `config/scopes/`, filled at the class instanciation
- `allowPublic` (boolean, protected) : Defines if all routes are reachable

## allowPublic() method

Modifies the `allowPublic` properties depending on the boolean parameter `allow` wich is by default set at true.

## getAuthMiddleware() method

Returns the middleware to call for authentication.
If the property `allowPublic` is set at true then the middlexare to call is `auth.public` else `auth`.

Then set `allowPublic` to false.

## all() method

Returns the list as an array of all possible scopes.

Example in json :

```json
{
    "client-get-access": "Récupérer tous les access",
    ...,
    "client-edit-users": "Gérer la modification d'utilisateurs",
    "user-get-access": "Récupérer tous les access",
    ...,
    "user-create-services": "Créer des services"
}
```

## getAllByCategories() method

Returns the list as a multidimensionnal array of all possible routes by categories.

Example in json :

```json
{
    "access": {
        "description": "Accès",
        "scopes": {
            "client-get-access": "Récupérer tous les access",
            "user-get-access": "Récupérer tous les access"
        }
    },
    "articles": {
        "description": "Articles",
        "scopes": {
            "client-manage-articles-actions-user": "Gérer les actions des utilisateurs sur les articles",
            ...,
            "user-create-articles": "Créer et faire suivre des articles"
        }
    },
    ...,
}
```

## nextVerbs() method

Returns a list of verbs as an array wich is the list a given verb childs (if `goUp` parameter is false) or parents (if `goUp` parameter is true)

### Parameters 

- String : $verb
- Boolean : $goUp (default false)

**verb**: verb to return de childs/parents of

**goUp**: boolean to indicate if childs (false, default), or parents (true) must be returned.

## find() method

Find an existing scope or throw errors.

### Parameters

- String : $scope

### Return value

It returns a multidumensional array of the scope configuration with it's childs. Empty array if the scope is empty.

Example in json for `client-get-articles-actions` :

```JSON
{
    "client-get-articles-actions": {
        "description": "Récupérer les actions des articles",
        "scopes": {
            "user": {
                "description": "Récupérer les actions des utilisateurs sur les articles"
            }
        }
    }
}
```

### Exceptions

It throws an error if:

- There is less than three word hypen seperated. A minimal scope is : `type-category-verb`
- There is a scope misconfiguration

## get() method

get an array as bellow (Example for `client-get-articles`) :

```JSON
{
    "client-get-articles": "Récupérer tout les articles"
}
```

It takes as argument a scope wich is use for a call of the `find()` method so the same constraints on the scope are applied here and the function will throw the same errors as the `find()` method.

It returns also an empty array if the scopes has not been found.

## getByCategories() method

Get all descriptions of every given scopes and agregate them by category. Useful to display decriptions of granted scopes for a user.

Same as for `find()` and `get()`, any given scope must respect the same conditions.

Example of result for the following input: `["user-get-assos", "user-manage-assos", "user-get-info"]`

```JSON
{
    "assos": {
        "description": "Associations",
        "scopes": [
            "Récupérer au nom de l'utilisateur les associations et leurs membres",
            "Gérer au nom de l'utilisateur les associations et leurs membres"
        ],
        "icon": "handshake"
    },
    "info": {
        "description": "Informations personnelles",
        "scopes": [
            "Récupérer toutes les informations de l'utilisateur"
        ],
        "icon": "user-circle"
    }
}
```

## getDevScopes() method

It returns scopes needed for developement in an array containing `user-get-access` and `user-manage-*`.
