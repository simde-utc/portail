/**
 * Création et gestion automatique et dynmaique du store géré par redux (store refait sur la base du travail d'Alexandre)
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <alexandre.brasseur@etu.utc.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license AGPL-3.0
**/

import produce from 'immer';
import { applyMiddleware, createStore } from 'redux';

// Import Middlewares
import promise from 'redux-promise-middleware';
import { createLogger } from 'redux-logger';
import thunk from 'redux-thunk';

// Suffixes des actions asynchrones
export const ASYNC_SUFFIXES = {
    loading: 'LOADING',
    success: 'SUCCESS',
    error: 'ERROR'
}

/**
 * ActionTypes Creator
 * Fonction qui permet de créer les types d'actions CRUD
 * @param      {string}   name    Le nom de la ressource au singulier en capital
 * @return     {Object}           Un set de types d'action CRUD pour la ressource name
 */
export const createCrudTypes = (name) => ({
    // _resource_name: name,
    getAll: 'GET_ALL_' + name,
    getOne: 'GET_ONE_' + name,
    create: 'CREATE_' + name,
    update: 'UPDATE_' + name,
    delete: 'DELETE_' + name
});

// Configure Middlewares
export const middlewares = applyMiddleware(
    thunk,
    promise({
        promiseTypeSuffixes: Object.values(ASYNC_SUFFIXES)
    }),
    // createLogger({ collapse: true })
);

// La racine du store
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
  findData: function (props, value, key = 'id', replacement = {}, forceReplacement = true) {
    var data = this.getData(props, []);

    for (let k in data) {
      if (data[k][key] === value) {
        if (!forceReplacement || !(data[k] instanceof Object) || Object.keys(data[k]).length > 0)
          return data[k];
      }
    }

    return replacement;
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
  resources: {},
};

// Racine de chaque catégorie CRUD
export const initialCrudState = {
  data: [],
  error: null,
  status: null,
  fetching: false,
  fetched: false,
  lastUpdate: null,
  resources: {},
};

// Comme le JS ne fait pas deep copy avec Object.assign, on est obligé de le faire nous-même..
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

// Ici, toute la magie opère, on génère dynmaiquement et automatiquement la route api et l'emplacement dans le store
export const buildStorePath = (store, path) => {
  var place = store.resources;
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

// Ici on crée le store et on modifie ses données via immer en fonction de la récup des données
export default createStore((state = initialState, action) => {
  console.log(action.type);
  if (action.meta && action.meta.path && action.meta.path.length > 0) {
    return produce(state, draft => {
        var path = action.meta.path;

        // Si on ne modifie qu'une donnée précise, il faut qu'on change le statut pour la ressource
        if (action.meta.action !== 'updateAll') {
          path = path.slice(0, -1);
        }

        if (action.type.endsWith('_' + ASYNC_SUFFIXES.loading)) {
          var place = buildStorePath(draft, path);

          place.fetching = true;
          place.status = null;
        }

        else if (action.type.endsWith('_' + ASYNC_SUFFIXES.success)) {
          var place = buildStorePath(draft, path);

          place.fetching = false;
          place.fetched = true;
          place.error = null;
          place.lastUpdate = action.meta.timestamp;
          place.status = action.payload.status;

          switch (action.meta.action) {
            case 'create':
              place.data.push(action.payload.data);
              break;

            case 'updateAll':
              place.data = action.payload.data;
              break;

            case 'update':
              var index = place.data.findIndex(data => data.id == action.payload.data.id);

              if (index === -1) {
                place.data.push(action.payload.data);
              }
              else {
                place.data[index] = action.payload.data;
              }

              break;

            case 'delete':
              var index = place.data.findIndex(data => data.id == action.payload.data.id);

              if (index > -1) {
                place.data.splice(index, 1);
              }

              break;
          }
        }

        else if (action.type.endsWith('_' + ASYNC_SUFFIXES.error)) {
          var place = buildStorePath(draft, path);

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
