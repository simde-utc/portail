import { combineReducers } from 'redux';
import { createCrudReducer } from './utils';
import actionTypes from './types';

const userReducer = createCrudReducer(actionTypes.user)
const assoReducer = createCrudReducer(actionTypes.asso)

export default combineReducers({
	user: userReducer,
	asso: assoReducer,
});
