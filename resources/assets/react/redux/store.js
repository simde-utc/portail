import { applyMiddleware, createStore } from 'redux';
import { ASYNC_SUFFIXES } from './utils';

// Import Middlewares
import { createLogger } from 'redux-logger';
import thunk from 'redux-thunk';

// Import Reducers
import reducers from './reducers';

// Configure Middlewares
const middlewares = applyMiddleware(
    thunk,
    createLogger({ collapse: true })
);

export default createStore(reducers, middlewares);
