import { combineReducers } from 'redux';

// Import Reducers
import userReducer from './user.js';
import assosReducer from './assos.js';
import articlesReducer from './articles.js';

// Combine Reducers
export default combineReducers({
	user: userReducer,
	assos: assosReducer,
    articles: articlesReducer
});
