import { combineReducers } from 'redux';
import { createCrudReducer } from './utils';
import actionTypes from './types';

const usersReducer = createCrudReducer(actionTypes.users)
const assosReducer = createCrudReducer(actionTypes.assos)
const articlesReducer = createCrudReducer(actionTypes.articles)

export default combineReducers({
	users: usersReducer,
	assos: assosReducer,
    articles: articlesReducer,
});
