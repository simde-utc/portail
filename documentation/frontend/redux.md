# Redux

Redux est utilisé pour gérer des données dans un unique store.
Toute l'implémentation de Redux est faite dans le dossier `redux/`.

Pour plus d'informations sur Redux, [regardez cette playlist](https://www.youtube.com/watch?v=1w-oQ-i1XB8&index=15&list=PLoYCgNOIyGABj2GQSlDRjgvXtqfDxKm5b).



## Les 3 parties de Redux

### Les types d'actions

Tout d'abord, il faut définir des **types d'actions**. Ce sont des chaînes de charactères constantes permettant d'identifier actions à effectuer dans les différents reducers. Il est conseillé de les définir dans le fichier `types.js`. Pour définir des set d'actions CRUD plus facilement, utilisez [`createCrudTypes`](#créateur-de-types-dactions).

### Les actions

Les actions sont des objets qui sont dispatchés aux reducers. Elles contienent généralement un `type` et un `payload`. Le gestionnaire d'action est défini dans le fichier `actions.js`. Il s'agit d'un Proxy couplé à une Classe qui permet ainsi de générer dynamiquement les actions désirées.

Les actions possibles sur les ressources sont alors:
- Requêtes Axios:
	+ `all`: Récupère toutes les ressources (args: queryParams)
	+ `find`/`one`/`get`: Récupère une ressource, pas forcément d'id nécessaire pour `one` (args: id, queryParams, jsonData)
	+ `create`: Crée une ressource (args: queryParams, jsonData)
	+ `update`: Met à jour une ressource (args: id, queryParams, jsonData)
	+ `delete`/`remove`: Supprime une ressource (args: id, queryParams, jsonData)
- Changement du store:
	+ `definePath`: Définie le chemin d'accès et de sauvegarde de la ressource dans le store (args: path)
	+ `addValidStatus`: Ajoute un status valide (args: status)
	+ `defineValidStatus`: Définie les status valide (args: status)
- N'importe quelle resource, permettant ainsi de nester la requête, par exemple: `actions.user.details.all()`.

Exemples:
```js
import actions from 'actions';

// Retrieve all semesters
dispatch(actions.semesters.all());
// Update an user's details
dispatch(actions.user.details.update(1, null, {}));
```


## Le store

Le store est l'endroit où toutes les ressources sont stockées.
Ici le store c'est un peu Superman ou Inspecteur Gadget, il est pété.
Le truc se contruit tout seul! Tout par de `resources`.

### Les reducers

Les reducers sont des fonctions de prototype : `function(prevState, action)`.

A partir de l'état actuel du store `prevState` et de l'action dispatchée `action`, ils retournent un nouvel état (potentiellement `prevState` si le reducer ne doit pas faire de modifications).

Il ne faut pas modifier `prevState` directement mais faire une copie. Ils sont définis dans le fichier `reducers.js`.




## Créateurs CRUD

Comme l'API du portail suit principalement le design CRUD (Create Read Update Delete), la plupart des actions Redux suivent aussi. C'est pourquoi nous avons créé des créateurs de types d'action, d'actions et de reducers CRUD. Ces fonctions se trouvent dans `react/utils.js`.

Les noms de ressources sont par convention mis au singulier.

### Créateur de types d'actions

`createCrudTypes(name)` permet de créer un set de types suivant le schéma suivant à partir d'un nom de resources `name` : 

Ce set de types est alors utilisé par les fonctions suivantes.


### Créateur d'actions

`createCrudActionSet(actionTypes, uri, overrides = {})` permet de créer un set d'actions CRUD à partir des paramètres suivants :
- `actionTypes` : un set d'actions généré à partir de la fonction `createCrudTypes`
- `uri` : le morceau d'url permettant d'accès au point de l'api concernant la ressource à partir de l'url de base. Par exemple `assos` permet d'accèder à `https://assos.utc.fr/api/v1/assos`
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





## Exemple d'utilisation des helpers CRUD

**Types** dans `redux/types.js`

```js
const articleTypes = createCrudTypes("ARTICLE")
```
qui correspond à :
```json
{
	getAll: 'GET_ALL_ARTICLE',
	getOne: 'GET_ONE_ARTICLE',
	create: 'CREATE_ARTICLE',
	update: 'UPDATE_ARTICLE',
	delete: 'DELETE_ARTICLE'
}
```



**Reducers** dans `redux/reducers.js`

```js
// État initial
const customInitialState = {
	...initialCrudState,
	firstCall: true
}
// Surcharge du reducer CRUD
const overrides = {
	CUSTOM_TYPE: function(state, action) {
		return { ...state, firstCall: false }
	}
}

const articleReducer = createCrudReducer(articleTypes, customInitialState, overrides)
```
qui correspond (de manière simplifiée et schématique) à :
```js
function(state = customInitialState, action) {
	switch (action.type) {
		// Surcharge via overrides
		'CUSTOM_TYPE':
			return overrides['CUSTOM_TYPE'](state, action)
			// return { ...state, firstCall: false }

		'GET_ALL_ARTICLE_LOADING':
			return { ...state, fetching: true, fetched: false }

		'GET_ALL_ARTICLE_ERROR':
			return { ...state, fetching: false, fetched: false, error: action.payload }

		'GET_ALL_ARTICLE_SUCCESS':
			if (action.meta.affectsAll) {
				return { ...state, data: action.payload.data }
			} else {
				// Modifie, ajoute ou supprime l'élément désiré
				// et renvoie la modification de l'état copié
			}
		// Si l'action n'est pas prise en charge par le reducer,
		// retourne l'état actuel sans modifications
		default:
			return state
	}
}
```



**Actions** dans `redux/actions.js`

```js
const overrides = {
	create: (data) => ({ type: 'CUSTOM_TYPE', payload: null }),
	newAction: (id, data, whatever) => ({ ... })
}
const userActions = createCrudActionSet(articleTypes, 'articles', overrides)
```
qui correspond à :
```js
{
	getAll: (queryParams = '') => ({
		type: 'GET_ALL_ARTICLE',
		meta: { affectsAll: true, arrayAction: 'updateAll', timestamp: Date.now() },
		payload: axios.get(`/api/v1/articles${queryParams}`)
	}),
	getOne: (id, queryParams = '') => ({
		type: 'GET_ONE_ARTICLE',
		meta: { affectsAll: false, arrayAction: 'update', timestamp: Date.now() },
		payload: axios.get(`/api/v1/articles/${id}${queryParams}`)
	}),
	// Remplacé
	create: (data) => ({ type: 'CUSTOM_TYPE', payload: null }),
	update: (id, data) => ({ ... }),
	delete: (id) => ({ ... }),
	// Surcharges
	newAction: (id, data, whatever) => ({ ... })
}
```


Utilisation dans un composant:
```js
this.props.dispatch(articleAction.create({ title: 'Article de test', description: '...', accent: "#ffffff" }))
```

