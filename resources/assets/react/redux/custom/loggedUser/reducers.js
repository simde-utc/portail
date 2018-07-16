import produce from 'immer';
import { ASYNC_SUFFIXES, initialCrudState } from '../../utils';
import loggedUserTypes from './types';


const loggedUserReducer = (state = { ...initialCrudState, data: {} }, action) => {
	// Override de data en objet null
	let reducerMap = {}
	
	// Async Actions started and failed
	Object.values(loggedUserTypes).forEach(type => {
		reducerMap[`${type}_${ASYNC_SUFFIXES.loading}`] = (state, action) => ({ ...state, fetching: true, fetched: false })
		reducerMap[`${type}_${ASYNC_SUFFIXES.error}`] 	= (state, action) => ({ ...state, fetching: false, fetched: false, error: action.payload })
	})

	// Update d'une propiété de l'utilisateur (info, roles...) 
	reducerMap[loggedUserTypes.updateUserProp + '_' + ASYNC_SUFFIXES.success] = (state, action) => produce(state, draft => {
		// Update status
		draft.fetching = false;
		draft.fetched = true;
		draft.lastUpdate = action.meta.timestamp

		// Update nested property with Lodash
		console.log(action.meta.nodePath)
		console.log(action.payload.data)
		draft.data[action.meta.nodePath] = action.payload.data
		// window._.set(draft.data, action.meta.nodePath, action.payload.data)
		return draft;
	})

	// Remove all
	reducerMap[loggedUserTypes.removeUser] = (state, action) => ({ ...state, fetching: false, fetched: false, date: {} })


	// Si on a l'action, changer d'état
	if (reducerMap.hasOwnProperty(action.type))
		return reducerMap[action.type](state, action)
	// Sinon rien
	return state;
}

export default loggedUserReducer;