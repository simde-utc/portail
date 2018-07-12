/**
 * ActionTypes Creator
 * Fonction qui permet de créer les types d'actions CRUD
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
	getAll: () => ({
		type: actionTypes.getAll,
		payload: axios.get(`/api/v1/${name}`)
	}),
	getOne: (id) => ({
		type: actionTypes.getOne,
		payload: axios.get(`/api/v1/${name}/${id}`)
	}),
	create: (data) => ({
		type: actionTypes.create,
		payload: axios.post(`/api/v1/${name}`)
	}),
	update: (id, data) => ({
		type: actionTypes.update,
		payload: axios.put(`/api/v1/${name}/${id}`)
	}),
	delete: (id) => ({
		type: actionTypes.delete,
		payload: axios.delete(`/api/v1/${name}/${id}`)
	}),
	...overrides
})


const initialCrudState = {
	data: null,
	error: null,
	fetching: false,
	fetched: false
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
	actionTypes.forEach(type => {
		reducerMap[`${type}_PENDING`] 	= {...state, fetching: true }
		reducerMap[`${type}_FULLFILED`] = {...state, fetching: false, data: action.payload }
		reducerMap[`${type}_REJECTED`] 	= {...state, fetching: false, error: action.payload }
	})

	return overrides[action.type] || reducer[action.type] || state;
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