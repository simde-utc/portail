import { applyMiddleware, createStore } from 'redux';
import { ASYNC_SUFFIXES } from './utils';

// Import Middlewares
import promise from 'redux-promise-middleware';
import { createLogger } from 'redux-logger'
import thunk from 'redux-thunk';

import reducers from './reducers.js';
const middleware = applyMiddleware(
	thunk,
	promise({
		promiseTypeSuffixes: Object.values(ASYNC_SUFFIXES)
	}),
	createLogger({ collapse: true })
);

export default createStore(reducers, middleware);
