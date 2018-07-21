import { combineReducers } from 'redux';
import { createCrudReducer } from './utils';
import crudActionTypes from './types';

// CRUD Reducers
const usersReducer = createCrudReducer(crudActionTypes.users)
const assosReducer = createCrudReducer(crudActionTypes.assos)

// Custom Reducers
import loggedUserReducer from './custom/loggedUser/reducers';


// Combine
export default combineReducers({
	// CRUD
	users: usersReducer,
	assos: assosReducer,

	// Custom
	loggedUser: loggedUserReducer,
});
