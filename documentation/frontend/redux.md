# Redux

Redux is used in order to manage datas in an single store.
All Redux's implementation is done in the `redux/` folder.

For more information on Redux, [have a look at this playlist](https://www.youtube.com/watch?v=1w-oQ-i1XB8&index=15&list=PLoYCgNOIyGABj2GQSlDRjgvXtqfDxKm5b).



## The 3 parts of Redux

### Action types

First, we have to define **action types**. They are constant charcaters strings which unable to identify actions to perform in the different reducers. It is recommanded to define in the `types.js` file. In order to define CRUD actions sets more easily, use [`createCrudTypes`](#créateur-de-types-dactions).

### Actions

Actions are objects that are qui sont scattered in the reducers. they generally content in a `type` and in a `payload`. They are defined in the `actions.js` file.


### Reducers

Reducers are prototype's fonctions : `function(prevState, action)`.

From the current status of the store `prevState` and of the scattered actions `action`, ils retournent un nouvel état (possibly `prevState` if the reducer has no to do modifications).

Do not directly modify `prevState`, make a copy of it. They are defined in the `reducers.js` file.




## CRUD creaters

As th portal's api mainly follows CRUD (Create Read Update Delete) design, most part of the Redux actions also follows. That is why we have created creaters of action types, of actions and of CRUD reducers. These functions are in `react/utils.js`.

Ressources names are conjugated in the singular by convention.

### Creater of action types

`createCrudTypes(name)` creates a set of types that follows the following scheme from a resources name `name` : 

This set of types is used by the following fuctions.


### Creater of actions

`createCrudActionSet(actionTypes, uri, overrides = {})` creates a set of CRUD actions from these parameters :
- `actionTypes` : a set of actions generated from the `createCrudTypes` function.
- `uri` : the url piece located after the domain name. It unables to access the right resource in the API. For example `assos` unables to access to `https://assos.utc.fr/api/v1/assos`
- `overrides` : an object unabling to replace and to add actions to the action set.


### Creater of reducer

`createCrudReducer(actionTypes, initialState = initialCrudState, overrides = {})` creates a CRUD reducer from the following parameters :
- `actionTypes` : an actions set generated from the `createCrudTypes` function
- `initialState` the initial state by default `initialCrudState`, can be replaced by extension of this one
- `overrides` : an object unabling to replace and to add actions gestions to the reducer.

```js
export const initialCrudState = {
	data: [],
	error: null,
	fetching: false,
	fetched: false,
	lastUpdate: null
}
```





## CRUD helpers use example 

**Types** in `redux/types.js`

```js
const articleTypes = createCrudTypes("ARTICLE")
```
which correspond to :
```json
{
	getAll: 'GET_ALL_ARTICLE',
	getOne: 'GET_ONE_ARTICLE',
	create: 'CREATE_ARTICLE',
	update: 'UPDATE_ARTICLE',
	delete: 'DELETE_ARTICLE'
}
```



**Reducers** in `redux/reducers.js`

```js
// Initial state
const customInitialState = {
	...initialCrudState,
	firstCall: true
}
// Overload of the CRUD reducer
const overrides = {
	CUSTOM_TYPE: function(state, action) {
		return { ...state, firstCall: false }
	}
}

const articleReducer = createCrudReducer(articleTypes, customInitialState, overrides)
```
which corresponds to (in a simplified way) à :
```js
function(state = customInitialState, action) {
	switch (action.type) {
		// Overload via overrides
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
				// Modifies, adds ou deletes the element
				// and returns the modification of the copied state
			}
		// If the action is not taken in charge by the reducer,
		// comes back to the actual state without modifications
		default:
			return state
	}
}
```



**Actions** in `redux/actions.js`

```js
const overrides = {
	create: (data) => ({ type: 'CUSTOM_TYPE', payload: null }),
	newAction: (id, data, whatever) => ({ ... })
}
const userActions = createCrudActionSet(articleTypes, 'articles', overrides)
```
which coresponds to :
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
	// Replaced
	create: (data) => ({ type: 'CUSTOM_TYPE', payload: null }),
	update: (id, data) => ({ ... }),
	delete: (id) => ({ ... }),
	// Overload
	newAction: (id, data, whatever) => ({ ... })
}
```


Use in a component:
```js
this.props.dispatch(articleAction.create({ title: 'Article de test', description: '...', accent: "#ffffff" }))
```

