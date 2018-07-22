import { combineReducers } from 'redux';
import { createCrudReducer } from './utils';
import crudActionTypes from './types';

const usersReducer = createCrudReducer(actionTypes.users)
const assosReducer = createCrudReducer(actionTypes.assos)
const articlesReducer = createCrudReducer(actionTypes.articles)

// Custom Reducers
import loggedUserReducer from './custom/loggedUser/reducers';


// Combine
export default combineReducers({
	// CRUD
	users: usersReducer,
	assos: assosReducer,
    articles: articlesReducer,
});
