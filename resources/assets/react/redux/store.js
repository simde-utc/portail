import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';

// Middlewares
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