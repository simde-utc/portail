import { applyMiddleware, createStore } from 'redux';
import { ASYNC_SUFFIXES } from './utils';

// Import Middlewares
import promise from 'redux-promise-middleware';
import { createLogger } from 'redux-logger';
import thunk from 'redux-thunk';

// Import Reducers
import reducers from './reducers';

// Configure Middlewares
const middlewares = applyMiddleware(
	thunk,
	promise({
		promiseTypeSuffixes: Object.values(ASYNC_SUFFIXES)
	}),
	createLogger({ collapse: true })
);

export default createStore(reducers, middlewares);
