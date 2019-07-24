# Redux

Redux is used in order to manage data in a unique store.
All Redux's implementation is done in the `redux/` folder.

For more information on Redux, [have a look at this playlist](https://www.youtube.com/watch?v=1w-oQ-i1XB8&index=15&list=PLoYCgNOIyGABj2GQSlDRjgvXtqfDxKm5b).



## The 3 parts of Redux

### Action types

First, we have to define **action types**. They are constant strings which unable to identify actions to perform in the different reducers. It is recommended to define them in the `types.js` file. In order to define CRUD actions sets more easily, use [`createCrudTypes`](#Action-types-creaters).

### Actions

Actions are objects that are scattered in the reducers. They generally content in a `type` and in a `payload`. They are defined in the `actions.js` file.


### Reducers

Reducers are functions with the following prototype: `function(prevState, action)`.

From the current status of the store `prevState` and of the dispatched action `action`, they return a new state (possibly `prevState` if the reducer doesn't have to do any modifications).

Do not directly modify `prevState`, make a copy of it. They are defined in the `reducers.js` file.




## CRUD creaters

As the portal's api mainly follows CRUD (Create Read Update Delete) design, most part of the Redux actions also follows it. That is why we have created creaters of action types, of actions and of CRUD reducers. These functions are in `react/utils.js`.

Ressources names are put in the singular by convention.

### Action types creators

`createCrudTypes(name)` creates a set of types that follows the following scheme from a resources name `name` : 

This set of types is used by the following functions.


### Creater of actions

`createCrudActionSet(actionTypes, uri, overrides = {})` creates a set of CRUD actions from these parameters :
- `actionTypes` : a set of actions generated from the `createCrudTypes` function.
- `uri` : the url piece located after the domain name. It enables to access the right resource in the API. For example `assos` unables to access to `https://assos.utc.fr/api/v1/assos`
- `overrides` : an object unabling to replace and to add actions to the action set.


### Creator of reducer

`createCrudReducer(actionTypes, initialState = initialCrudState, overrides = {})` creates a CRUD reducer from the following parameters :
- `actionTypes` : an actions set generated from the `createCrudTypes` function
- `initialState` the initial state by default `initialCrudState`, can be replaced by extension of this one
- `overrides` : an object that enable to replace and to add actions gestions to the reducer.

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
which corresponds to (in a simplified way):
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
				// Modifies, adds or deletes the element
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

