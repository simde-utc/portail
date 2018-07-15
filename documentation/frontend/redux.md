# Redux

Redux est utilisé pour gérer les données dans un store.
Toute l'implémentation de Redux est faite dans le dossier `redux/`.

Pour plus d'informations sur Redux, [regardez cette playlist](https://www.youtube.com/watch?v=1w-oQ-i1XB8&index=15&list=PLoYCgNOIyGABj2GQSlDRjgvXtqfDxKm5b).


## Créateurs CRUD

Comme l'API du portail suit principalement le design CRUD (Create Read Update Delete), la plupart des actions Redux suivent aussi. C'est pourquoi nous avons créé des créateurs de types d'action, d'actions et de reducers CRUD. C'est fonctions se trouvent dans `react/utils.js`.

Les noms de ressources sont par convention mis au pluriel.

### Créateur de types d'actions

`createCrudTypes(name)` permet de créer un set de types suivant le schéma suivant à partir d'un nom de resources `name` : 

```js
createCrudTypes("OBJET")
{
    getAll: "GET_ALL_OBJECT",
    getOne: "GET_ONE_OBJECT",
    create: "CREATE_OBJECT",
    update: "UPDATE_OBJECT",
    delete: "DELETE_OBJECT"
}
```

Ce set de types est alors utilisé par les fonctions suivantes.


### Créateur d'actions

`createCrudActionSet(actionTypes, uri, overrides = {})` permet de créer un set d'actions CRUD à partir des paramètres suivants :
- `actionTypes` : un set d'actions généré à partir de la fonction `createCrudTypes`
- `uri` : le morceau d'url permettant d'accès au point de l'api concernant la ressource à partir de l'url de base. Par exemple `assos` permet d'accèder à _https://assos.utc.fr/api/v1/**assos**_
- `overrides` : un objet permettant de remplacer et d'ajouter des actions au set d'actions


### Créateur de reducer

`createCrudReducer(actionTypes, initialState = initialCrudState, overrides = {})` permet de créer un reducer CRUD à partir des paramètres suivants :
- `actionTypes` : un set d'actions généré à partir de la fonction `createCrudTypes`
- `initialState` le state initial, par défaut `initialCrudState`, pouvant être remplacé par une extension de celui-ci
- `overrides` : un objet permettant de remplacer et d'ajouter des gestions d'actions au reducer

```js
export const initialCrudState = {
    data: [],
    error: null,
    fetching: false,
    fetched: false,
    lastUpdate: null
}
```


## Exemple d'utilisation

Types dans `react/types.js`
```js
const userTypes = createCrudTypes("USER")
```


Reducers dans `react/reducers.js`
```js
const customInitialState = {
    ...initialCrudState,
    firstCall: false
}
const overrides = {
    CUSTOM_TYPE: function(state, action) { ... }
}
const usersReducer = createCrudReducer(userTypes, customInitialState, overrides)
```


Actions dans `react/actions.js`
```js
const overrides = {
    create: function(data) { ... },
    newAction: function(id, data, whatever) { ... 
}
const userActions = createCrudActionSet(userTypes, 'users', overrides)
```


Utilisation dans un composant
```js
this.props.dispatch(usersAction.create({ ... }))
```

