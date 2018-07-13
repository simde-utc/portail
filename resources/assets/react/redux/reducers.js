import { combineReducers } from 'redux';
import { createCrudReducer } from './utils';
import actionTypes from './types';

const usersReducer = createCrudReducer(actionTypes.users)
const assosReducer = createCrudReducer(actionTypes.assos)

export default combineReducers({
	users: usersReducer,
	assos: assosReducer,
});
