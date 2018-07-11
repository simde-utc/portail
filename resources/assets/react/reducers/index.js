import { combineReducers } from 'redux';

// Import Reducers
import userReducer from './user.js';
import assosReducer from './assos.js';

// Combine Reducers
export default combineReducers({
	user: userReducer,
	assos: assosReducer
});
