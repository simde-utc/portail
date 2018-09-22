import produce from 'immer';
import { applyMiddleware, createStore } from 'redux';
import { ASYNC_SUFFIXES } from './utils';

// Import Middlewares
import findIndex from 'lodash';
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
    // createLogger({ collapse: true })
);

export const initialState = {
  // Converti tout simple une route uri (string) en array | ex: 'assos/calendars' => ['assos', 'calendars']
  propsToArray: function (props) {
    if (typeof props === 'string') {
      props = props.split('/');
    }

    if (!(props instanceof Array)) {
      return [];
    }

    return props;
  },

  // Permet de retouver facilement un élément du store (remplacement est par quoi on replace si on trouve pas, et on force si array vide par ex)
  get: function (props, replacement = {}, forceReplacement = false) {
    var data = this;
    props = this.propsToArray(props)

    for (let key in props) {
      if (data[props[key]] !== undefined) {
        data = data[props[key]]
      }
      else if (data.resources && data.resources[props[key]] !== undefined) {
        data = data.resources[props[key]]
      }
      else {
        return replacement;
      }
    }

    if (forceReplacement && (data instanceof Object) && Object.keys(data).length === 0)
      return replacement;

    return data;
  },

  // Retrouver un élément précis
  getData: function (props, replacement = [], forceReplacement = true) {
    return this.get(this.propsToArray(props).concat(['data']), replacement, forceReplacement);
  },
  getError: function (props, replacement = null, forceReplacement = true) {
    return this.get(this.propsToArray(props).concat(['error']), replacement, forceReplacement);
  },
  getStatus: function (props, replacement = null, forceReplacement = true) {
    return this.get(this.propsToArray(props).concat(['status']), replacement, forceReplacement);
  },
  getLastUpdate: function (props, replacement = null, forceReplacement = true) {
    return this.get(this.propsToArray(props).concat(['lastUpdate']), replacement, forceReplacement);
  },
  isFetching: function (props, replacement = false, forceReplacement = true) {
    return this.get(this.propsToArray(props).concat(['fetching']), replacement, forceReplacement);
  },
  isFetched: function (props, replacement = false, forceReplacement = true) {
    return this.get(this.propsToArray(props).concat(['fetched']), replacement, forceReplacement);
  },
};

export const initialCrudState = {
  data: [],
  error: null,
  status: null,
  fetching: false,
  fetched: false,
  lastUpdate: null,
  resources: {},
  find: (value, key = 'id') => {

  }
};

export const initCrudState = (state, initialState = initialCrudState) => {
  for (let key in initialState) {
    if (initialState.hasOwnProperty(key)) {
      if (initialState[key] instanceof Object) {
        state[key] = Array.isArray(initialState[key]) ? [] : {};
        initCrudState(state[key], initialState[key]);
      }
      else {
        state[key] = initialState[key];
      }
    }
  }

  return state;
}

export const buildStorePath = (store, path) => {
  var place = store;
  var part, isId = false;

  for (let key in path) {
    part = path[key];
    isId = false;

    if (place[part] !== undefined) {
      place = place[part];
    }
    else if (place.resources !== undefined) {
      if (part.startsWith('id:')) {
        part = part.slice(3);
        isId = true;
      }

      if (place.resources[part] === undefined) {
        place.resources[part] = {};
      }

      place = place.resources[part];
    }
    else {
      place[part] = {};
      initCrudState(place[part]);

      place = place[part];
    }
  }

  return place;
}

export default createStore((state = initialState, action) => {
  console.log(action.type);

  if (action.meta && action.meta.path && action.meta.path.length > 0) {
    return produce(state, draft => {
        if (action.type.endsWith('_' + ASYNC_SUFFIXES.loading)) {
          var place = buildStorePath(draft, action.meta.path);
          initCrudState(place);

          place.fetching = true;
          place.status = null;
        }

        else if (action.type.endsWith('_' + ASYNC_SUFFIXES.success)) {
          var place = buildStorePath(draft, action.meta.path);

          place.fetching = false;
          place.fetched = true;
          place.error = null;
          place.lastUpdate = action.meta.timestamp;
          place.data = action.payload.data;
          place.status = action.payload.status;
        }

        else if (action.type.endsWith('_' + ASYNC_SUFFIXES.error)) {
          var place = buildStorePath(draft, action.meta.path);

          place.fetching = false;
          place.fetched = false;
          place.error = action.payload;
          place.status = action.payload.status;
        }

        return draft;
    });
  }

  return state;
}, middlewares);
