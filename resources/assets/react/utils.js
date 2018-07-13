import produce from 'immer';

/**
 * ActionTypes Creator
 * Fonction qui permet de créer les types d'actions CRUD
 * @param      {string}   name    Le nom de la ressource au singulier en capital 
 */
export const createActionTypes = (name) => ({
	getAll: `GET_ALL_${name}`,
	getOne: `GET_ONE_${name}`,
	create: `CREATE_${name}`,
	update: `UPDATE_${name}`,
	delete: `DELETE_${name}`
});

/**
 * ActionCreator Creator
 * Fonction qui permet de générer un set d'actions CRUD
 * @param      {Object}   actionTypes    Les types d'actions possibles 
 * @param      {string}   uri            L'uri CRUD de la ressource
 * @param      {Object}   overrides      Surcharge du set d'actions
 */
export const createCrudActionSet = (actionTypes, uri, overrides = {}) => ({
	getAll: (queryParams = '') => ({
		type: actionTypes.getAll,
		meta: { affectsAll: true, arrayAction: 'update', timestamp: Date.now() },
		payload: axios.get(`/api/v1/${uri}${queryParams}`)
	}),
	getOne: (id, queryParams = '') => ({
		type: actionTypes.getOne,
		meta: { affectsAll: false, arrayAction: 'update', timestamp: Date.now() },
		payload: axios.get(`/api/v1/${uri}/${id}${queryParams}`)
	}),
	create: (data) => ({
		type: actionTypes.create,
		meta: { affectsAll: false, arrayAction: 'insert', timestamp: Date.now() },
		payload: axios.post(`/api/v1/${uri}`)
	}),
	update: (id, data) => ({
		type: actionTypes.update,
		meta: { affectsAll: false, arrayAction: 'update', timestamp: Date.now() },
		payload: axios.put(`/api/v1/${uri}/${id}`)
	}),
	delete: (id) => ({
		type: actionTypes.delete,
		meta: { affectsAll: false, arrayAction: 'delete', timestamp: Date.now() },
		payload: axios.delete(`/api/v1/${uri}/${id}`)
	}),
	...overrides
})

const initialCrudState = {
	data: [],
	error: null,
	fetching: false,
	fetched: false,
	lastUpdate: null
}
/**
 * Reducer Creator
 * Génère un reducer à partir d'un set d'action CRUD
 * @param      {Object}  actionTypes   Les types d'actions
 * @param      {Object}  initialState  Le state par défaut
 * @param      {Object}  overrides     Surchage de la map d'actions du reducer
 * @return     {Object}  Un reducer
 */
export const createCrudReducer = (actionTypes, initialState = initialCrudState, overrides = {}) => (state = initialState, action) => {
	let reducerMap = {}
	Object.values(actionTypes).forEach(type => {
		// Request started
		reducerMap[`${type}_PENDING`] 	= (state, action) => ({...state, fetching: true, fetched: false })

		// Request succeeded
		reducerMap[`${type}_FULFILLED`] = produce((draft, action) => {
			draft.fetching = false;
			draft.fetched = true;
			if (action.meta.affectsAll) {
				// Full copy if affects all data
				draft.data = action.payload.data;
			} else {
				// Single element copy
				let idx;
				switch (action.meta.arrayAction) {
					case 'create':
						draft.data.push(action.payload.data);
						break;
					case 'update':
						idx = draft.data.indexOf(resource => resource.id == action.meta.id);
						if (idx === -1)		// Create if it doesn't exist
							draft.data.push(action.payload.data);
						else
							draft.data[idx] = action.payload.data;
						break;
					case 'delete':
						idx = draft.data.indexOf(resource => resource.id == action.meta.id);
						draft.data.splice(idx, 1);
						break;
				}
			}
		})
		
		// Request failed
		reducerMap[`${type}_REJECTED`] 	= (state, action) => ({...state, fetching: false, fetched: false, error: action.payload })
	})

	if (overrides.hasOwnProperty(action.type))
		return overrides[action.type](state, action)
	if (reducerMap.hasOwnProperty(action.type)) {
		return reducerMap[action.type](state, action)
	}
	return state;
}


/**
 * Examples d'utilisation

// ActionTypes
const userTypes = createActionTypes("USER")

// Reducer
const userReducer = createCrudReducer(userTypes, {}, overrides?)

// Actions
const userActions = createCrudActionSet(userTypes, 'users', { create: (data) => { ... } })
usersAction.create({...})

*/