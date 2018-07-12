import { applyMiddleware, createStore } from 'redux';

// Import Middlewares
import promise from 'redux-promise-middleware';
import { createLogger } from 'redux-logger'
import thunk from 'redux-thunk';

import reducers from './reducers.js';
const middleware = applyMiddleware(
	thunk,
	promise(),
	createLogger({ collapse: true })
);

export default createStore(reducers, middleware);
