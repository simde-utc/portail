# Droits API avec OAuth
*(customisé par Samy parce qu'il est trop fort)*

## Présentation rapide

Un token est un triplet (client, user, scopes). Ce token est récupéré par un `client` (qui est une API/site/interface) d'une association ou par un `user` un utilisateur (connecté ou API personnelle).

## Middleware

Le Middleware:
- `auth:api` permet de vérifier que la connexion est bien faite par un client (asso ou perso) lié à un utlisateur.
- `auth.client` permet de vérifier que la connexion est bien faite par un client non lié à un utlisateur.
- `auth.any` permet de vérifier que la connexion est bien faite par un client (asso ou perso)

Ces Middlewares sont générés automatiquement par la façade `Scopes`.

## Scopes

Les Scopes permettent de définir une portée autorisée pour un client. Ils permettent à un client d'accéder ou non à une route/ressource en fonction des Scopes prérequis par celle-ci.

### Liste des scopes en fonction des routes

#### Définition des scopes:

portée-verbe-categorie + (pour chaque sous-catégorie: -sousCatégorie)
Par exemple : `user-get-info`, `user-get-assos`, `user-get-assos-followed-now`


#### Définition de la portée des scopes:

- **user** : nécessite que l'application soit connecté à un utilisateur
- **client** :  nécessite que l'application ait les droits d'application indépendante d'un utilisateur


#### Définition du verbe:

Les actions sont hiérarchisées. Chaque scope supérieur permet aussi les scopes enfants.
- **manage**:  gestion de la ressource entière
    + **set** :  posibilité d'écrire et modifier les données
        * **create**:  créer une donnée associée
        * **edit**:    modifier une donnée
        * **remove**:  supprimer une donnée
    + **get** :  récupération des informations en lecture seule

### Facades

La façade `Scopes` permet de simplifier l'utilisatation des Scopes.

Ces méthodes génèrent les middlewares qui sont requis à la demande:
- `matchAnyUser()`: autorise uniquement les clients rattachés à un utilisateur
- `matchAnyClient()`: autorise uniquement les clients non rattachés à un utilisateur
- `matchAnyUserOrClient()`: autorise uniquement les clients connectés
- `match($scopes, array $scopes2)`: détecte en fonction des arguments donnés:
  - si `$scopes` est un `string`: renvoie `matchOne($scopes)`
  - si `$scopes` est un `array` : renvoie `matchAll($scopes, $scopes)`
- `matchOne($scopes)`: autorise uniquement si le client possède un des scopes parmi la liste donnée ou de leurs héritiés
- `matchAll(array $scopes, array $scopes2)`: autorise uniquement si le client possède tous les `$scopes` parmi la liste donnée ou leurs héritiés, ou tous les `$scopes2` parmi la liste 2 donnée ou leurs héritiés

Des fonctions similaires existent qui renvoie un booléen plutôt que d'autoriser via des middlewares.

### Utiliser les scopes dans les controllers du portail

