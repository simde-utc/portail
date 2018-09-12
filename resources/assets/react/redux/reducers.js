import { combineReducers } from 'redux';
import { createCrudReducer } from './utils';
import crudActionTypes from './types';

const usersReducer = createCrudReducer(crudActionTypes.users)
const assosReducer = createCrudReducer(crudActionTypes.assos)
const articlesReducer = createCrudReducer(crudActionTypes.articles)

// Custom Reducers
import loggedUserReducer from './custom/loggedUser/reducers';


// Combine
export default combineReducers({
	// CRUD
	users: usersReducer,
	assos: assosReducer,
	articles: articlesReducer,
	loggedUser: loggedUserReducer
});
