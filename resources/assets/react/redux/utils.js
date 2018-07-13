import produce from 'immer';


// Suffixes des actions asynchrones
export const ASYNC_SUFFIXES = {
	loading: 'LOADING',
	success: 'SUCCESS',
	error: 'ERROR'
}


/**
 * ActionTypes Creator
 * Fonction qui permet de créer les types d'actions CRUD
 * @param      {string}   name    Le nom de la ressource au singulier en capital 
 */
export const createCrudTypes = (name) => ({
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
export const createCrudActions = (actionTypes, uri, overrides = {}) => ({
	getAll: (queryParams = '') => ({
		type: actionTypes.getAll,
		meta: { affectsAll: true, arrayAction: 'updateAll', timestamp: Date.now() },
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


export const initialCrudState = {
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
	// reducerMap est une map (action => reducer function) créée à partir des actionTypes
	let reducerMap = {}
	Object.values(actionTypes).forEach(type => {
		// Request started
		reducerMap[`${type}_${ASYNC_SUFFIXES.loading}`] = (state, action) => ({...state, fetching: true, fetched: false })

		// Request succeeded
		// Ici on utilise le package immer qui s'occupe de l'immutabilité du state par draft
		reducerMap[`${type}_${ASYNC_SUFFIXES.success}`] = (state, action) => produce(state, draft => {
			// Update status
			draft.fetching = false;
			draft.fetched = true;
			draft.lastUpdate = action.meta.timestamp

			if (action.meta.affectsAll) {
				// Full copy if affects all data
				draft.data = action.payload.data;
			} else {
				// Single element copy
				let idx;
				switch (action.meta.arrayAction) {
					case 'create':
						// Add new object to array
						draft.data.push(action.payload.data);
						break;
					case 'update':
						// Get the index of the resource in the array
						idx = draft.data.findIndex(resource => resource.id == action.payload.data.id);
						if (idx === -1)		// Create if it doesn't exist
							draft.data.push(action.payload.data);
						else				// Else modify
							draft.data[idx] = action.payload.data;
						break;
					case 'delete':
						// Get the index of the resource in the array
						idx = draft.data.findIndex(resource => resource.id == action.payload.data.id);
						if (idx !== -1) 	// Delete data if exists
							draft.data.splice(idx, 1);
						break;
				}
			}
			return draft;
		})
		
		// Request failed
		reducerMap[`${type}_${ASYNC_SUFFIXES.error}`] 	= (state, action) => ({...state, fetching: false, fetched: false, error: action.payload })
	})

	// Si le gestionnaire d'action est surchargé, utiliser cette version
	if (overrides.hasOwnProperty(action.type))
		return overrides[action.type](state, action)
	// Sinon s'il s'agit d'une action CRUD, utiliser celle de reducerMap
	if (reducerMap.hasOwnProperty(action.type))
		return reducerMap[action.type](state, action)
	// Sinon rien
	return state;
}

