/**
 * Création et gestion automatique et dynmaique du store géré par redux (store refait sur la base du travail d'Alexandre)
 *
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
export const store = {
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
            else if (data.resources[props[key]] !== undefined) {
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
        // Les ressources sont rangées par id:
        if (key === 'id') {
            return this.getData(this.propsToArray(props).concat(['value']), replacement, forceReplacement);
        }
        else {
            var data = this.getData(props, []);

            for (let k in data) {
                if (data[k][key] === value) {
                    if (!forceReplacement || !(data[k] instanceof Object) || Object.keys(data[k]).length > 0) {
                        return data[k];
                    }
                }
            }
        }

        return replacement;
    },
    getError: function (props, replacement = null, forceReplacement = true) {
        return this.get(this.propsToArray(props).concat(['error']), replacement, forceReplacement);
    },
    hasFailed: function (props, replacement = false, forceReplacement = true) {
        return this.get(this.propsToArray(props).concat(['failed']), replacement, forceReplacement);
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
    // Permet de savoir si une requête s'est terminée
    hasFinished: function (props, replacement = false, forceReplacement = true) {
        return this.get(this.propsToArray(props).concat(['fetched']), replacement, forceReplacement) ||
            this.get(this.propsToArray(props).concat(['failed']), replacement, forceReplacement);
    },
    resources: {},
};

// Racine de chaque catégorie CRUD
export const initialCrudState = {
    data: [],
    error: null,
    failed: false,
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
    var place = store;
    var part;

    for (let key in path) {
        part = path[key];

        if (place.resources[part] === undefined) {
            place.resources[part] = {};
            initCrudState(place.resources[part]);
        }

        place = place.resources[part];
    }

    return place;
}

export const makeResourceSuccessed = (place, timestamp, status) => {
    place.fetching = false;
    place.fetched = true;
    place.error = null;
    place.failed = false;
    place.lastUpdate = timestamp;
    place.status = status;
};

// Ici on crée le store et on modifie ses données via immer en fonction de la récup des données
export default createStore((state = store, action) => {
    console.debug(action.type);
    if (action.meta && action.meta.path && action.meta.path.length > 0) {
        return produce(state, draft => {
            var path = action.meta.path;
            var id;

            // Si on ne modifie qu'une donnée précise, il faut qu'on change le statut pour la ressource
            if (action.meta.action !== 'updateAll') {
                path = path.slice();
                id = path.pop();
            }

            var place = buildStorePath(draft, path);

            if (action.type.endsWith('_' + ASYNC_SUFFIXES.loading)) {
                place.fetching = true;
                place.status = null;
            }
            // Si on a défini que la réponse HTTP était valide:
            else if (action.meta.validStatus.includes(action.payload.status || action.payload.response.status)) {
                if (action.type.endsWith('_' + ASYNC_SUFFIXES.success)) {
                    const { timestamp, status, data } = action.payload;
                    makeResourceSuccessed(place, timestamp, status);

                    if (action.meta.action === 'updateAll') {
                        place.data = data;

                        if (Array.isArray(data)) {
                            for (let key in data) {
                                var element = data[key];
                                var placeForData = buildStorePath(draft, path.concat([element.id]));

                                makeResourceSuccessed(placeForData, timestamp, status);
                                placeForData.data = element;
                            }
                        }
                        else if (data.id) {
                            var placeForData = buildStorePath(draft, path.concat([data.id]));

                            makeResourceSuccessed(placeForData, timestamp, status);
                            placeForData.data = data;
                        }
                        else {
                            var keys = Object.keys(data);

                            for (let key in keys) {
                                var element = data[keys[key]];
                                var placeForData = buildStorePath(draft, path.concat([keys[key]]));

                                makeResourceSuccessed(placeForData, timestamp, status);
                                placeForData.data = element;
                            }
                        }
                    }
                    else {
                        // On stock la data dans la liste des données de la ressource
                        switch (action.meta.action) {
                            case 'update':
                                var index = place.data.findIndex(dataFromPlace => dataFromPlace.id === data.id);

                                if (index > -1) {
                                    place.data[index] = data;

                                    break;
                                }

                            case 'create':
                                place.data.push(data);

                                break;

                            case 'delete':
                                var index = place.data.findIndex(dataFromPlace => dataFromPlace.id === data.id);

                                if (index > -1) {
                                    place.data.splice(index, 1);
                                }

                                break;
                        }

                        // On stock la data par id pour la ressource
                        switch (action.meta.action) {
                            case 'update':
                            case 'create':
                                // On modifie/stock la donnée via l'id
                                var placeForData = buildStorePath(draft, path.concat([id]));

                                makeResourceSuccessed(placeForData, timestamp, status);
                                placeForData.data = data;

                                break;

                            case 'delete':
                                delete place.resources[id];

                                break;
                        }

                        // Typiquement, si on a une asso et qu'on la recherche par login
                        if (id !== data.id) {
                            // On stock la data par id pour la ressource
                            switch (action.meta.action) {
                                case 'update':
                                case 'create':
                                    // On modifie/stock la donnée via l'id de la data
                                    var placeForIdData = placeForData;
                                    var placeForData = buildStorePath(draft, path.concat([data.id]));

                                    makeResourceSuccessed(placeForData, timestamp, status);
                                    placeForData.data = data;
                                    placeForIdData.resources = placeForData.resources;

                                    break;

                                case 'delete':
                                    delete place.resources[data.id];

                                    break;
                            }
                        }
                    }
                }
            }

            else if (action.type.endsWith('_' + ASYNC_SUFFIXES.error)) {
                if (id) {
                    place = buildStorePath(draft, path.concat([id]));
                }

                place.data = [];
                place.fetching = false;
                place.fetched = false;
                place.error = action.payload;
                place.failed = true;
                place.status = action.payload.response.status;
            }
            // On a un success du côté de Redux mais on refuse de notre côté le code HTTP
            else {
                var place = buildStorePath(draft, path);

                place.data = [];
                place.fetching = false;
                place.fetched = false;
                place.error = 'NOT ACCEPTED';
                place.failed = true;
                place.status = action.payload.status;
            }

            return draft;
        });
    }

    return state;
}, middlewares);
