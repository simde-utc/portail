import produce from 'immer';
import { ASYNC_SUFFIXES, initialCrudState } from '../../utils';
import loggedUserTypes from './types';
import loggedUserActions from './actions';

const loggedUserInitialState = {
	...initialCrudState,
	data: {},
	lastUpdate: null,
	isAuthenticated: function() {
		return Object.keys(this.data).length > 0
	}
}

const loggedUserReducer = (state = loggedUserInitialState, action) => {
	// Override de data en objet null
	let reducerMap = {}

	// Async Actions started and failed
	Object.values(loggedUserTypes).forEach(type => {
		reducerMap[`${type}_${ASYNC_SUFFIXES.loading}`] = (state, action) => ({ ...state, fetching: true, fetched: false })
		reducerMap[`${type}_${ASYNC_SUFFIXES.error}`] 	= (state, action) => produce(state, draft => {
			// Update status
			draft.fetching = false;
			draft.fetched = false;
			draft.lastUpdate = action.meta.timestamp

			// Unauthenticated => Clear user
			if (action.payload.response.status == 401)
				draft.data = {};
			return draft;	
		})
	})

	// Update d'une propiété de l'utilisateur (info, roles...) 
	reducerMap[loggedUserTypes.updateUserProp + '_' + ASYNC_SUFFIXES.success] = (state, action) => produce(state, draft => {
		// Update status
		draft.fetching = false;
		draft.fetched = true;
		draft.lastUpdate = action.meta.timestamp

		// Update property
		draft.data[action.meta.nodePath] = action.payload.data
		// Update nested property with Lodash
		// window._.set(draft.data, action.meta.nodePath, action.payload.data)
		return draft;
	})

	// Remove all
	reducerMap[loggedUserTypes.removeUser] = (state, action) => ({ ...state, fetching: false, fetched: false, data: {} })


	// Si on a l'action, changer d'état
	if (reducerMap.hasOwnProperty(action.type))
		return reducerMap[action.type](state, action)
	// Sinon rien
	return state;
}

export default loggedUserReducer;