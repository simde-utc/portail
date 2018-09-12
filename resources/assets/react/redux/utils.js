import produce from 'immer';


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


/**
 * ActionCreator Creator
 * Fonction qui permet de générer un set d'actions CRUD
 * @param      {Object}   actionTypes    Les types d'actions possibles
 * @param      {string}   uri            L'uri CRUD de la ressource
 * @param      {string}   overrides      Surcharge du set d'actions
 * @return     {Object}                  Un set de d'action CRUD pour les types actionTypes
 */
export class crudActions {
    constructor(actionTypes, uri, overrides) {
        this.rootUri = '/api/v1/'
        this.uriRegex = /{([^{]+)}/g
        this.uriParams = {}
        this.actionTypes = actionTypes
        this.uri = uri

        for (key in overrides) {
            if (overrides.hasOwnProperty(key))
                this[key] = overrides[key]
        }
    }

    setUriParams(uriParams) {
        this.uriParams = uriParams

        return this
    }

    compileQuery(queryParams, prefix) {
        var queries = []

        for (key in queryParams) {
            if (queryParams.hasOwnProperty(key)) {
                if (Array.isArray(queryParams[key]) || Object.isObject(queryParams[key]))
                    queries.push(compileQuery(queryParams[key], true))
                else
                    query.push(encodeURIComponent(prefix ? ('[' + key + ']') : key) + '=' + encodeURIComponent(queryParams[key]))
            }
        }

        return queries.join('&')
    }

    compileUri(uri, uriParams) {
        return uri.replace(this.uriRegex, (ignore, key) => {
            return (key = uriParams[key]) == null ? '' : key
        });
    }

    getFullUri(uri, uriParams, queryParams) {
        var queries = this.compileQuery(queryParams)

        return this.compileUri(uri, uriParams) + (queries.length === 0 ? '' : ('?' + queries))
    }

    getAll(queryParams = {}) {
        return {
            type: this.actionTypes.getAll,
            meta: { affectsAll: true, arrayAction: 'updateAll', timestamp: Date.now() },
            payload: window.axios.get(this.getFullUri(this.rootUri + this.uri, this.uriParams, this.queryParams))
        }
    }

    getOne(id, queryParams = {}) {
        return {
            type: this.actionTypes.getOne,
            meta: { affectsAll: false, arrayAction: 'update', timestamp: Date.now() },
            payload: window.axios.get(this.getFullUri(this.rootUri + this.uri + '/' + id, this.uriParams, this.queryParams))
        }
    }

    create(data, queryParams = {}) {
        return {
          type: this.actionTypes.create,
          meta: { affectsAll: false, arrayAction: 'insert', timestamp: Date.now() },
          payload: window.axios.post(this.getFullUri(this.rootUri + this.uri, this.uriParams, this.queryParams), data)
        }
    }

    update(id, data, queryParams = {}) {
        return {
            type: this.actionTypes.update,
            meta: { affectsAll: false, arrayAction: 'update', timestamp: Date.now() },
            payload: window.axios.put(this.getFullUri(this.rootUri + this.uri + '/' + id, this.uriParams, this.queryParams), data)
        }
    }

    remove(id, queryParams = {}) {
        return {
            type: this.actionTypes.delete,
            meta: { affectsAll: false, arrayAction: 'delete', timestamp: Date.now() },
            payload: window.axios.delete(this.getFullUri(this.rootUri + this.uri + '/' + id, this.uriParams, this.queryParams))
        }
    }
}

// L'état initial pour les ressources CRUD
export const initialCrudState = {
    data: [],
    error: null,
    fetching: false,
    fetched: false,
    lastUpdate: null
}

/**
 * Reducer Creator
 * Génère un reducer à partir d'un set d'action CRUD
 * @param      {Object}  actionTypes   Les types d'actions
 * @param      {Object}  initialState  Le state par défaut
 * @param      {Object}  overrides     Surchage de la map d'actions du reducer
 * @return     {Object}                Un reducer CRUD
 */
export const createCrudReducer = (actionTypes, initialState = initialCrudState, overrides = {}) => (state = initialState, action) => {
    // reducerMap est une map (action => reducer function) créée à partir des actionTypes
    let reducerMap = {}
    Object.values(actionTypes).forEach(type => {
        // Request started
        reducerMap[`${type}_${ASYNC_SUFFIXES.loading}`] = (state, action) => ({ ...state, fetching: true, fetched: false })

        // Request succeeded
        // Ici on utilise le package immer qui s'occupe de l'immutabilité du state par draft
        reducerMap[`${type}_${ASYNC_SUFFIXES.success}`] = (state, action) => produce(state, draft => {
            // Update status
            draft.fetching = false;
            draft.fetched = true;
            draft.lastUpdate = action.meta.timestamp

            if (action.meta.affectsAll) {
                // Full copy if affects all data
                draft.data = action.payload.data;
            } else {
                // Single element copy
                let idx;
                switch (action.meta.arrayAction) {
                    case 'create':
                        // Add new object to array
                        draft.data.push(action.payload.data);
                        break;
                    case 'update':
                        // Get the index of the resource in the array
                        idx = draft.data.findIndex(resource => resource.id == action.payload.data.id);
                        if (idx === -1)     // Create if it doesn't exist
                            draft.data.push(action.payload.data);
                        else                // Else modify
                            draft.data[idx] = action.payload.data;
                        break;
                    case 'delete':
                        // Get the index of the resource in the array
                        idx = draft.data.findIndex(resource => resource.id == action.payload.data.id);
                        if (idx !== -1)     // Delete data if exists
                            draft.data.splice(idx, 1);
                        break;
                }
            }
            return draft;
        })

        // Request failed
        reducerMap[`${type}_${ASYNC_SUFFIXES.error}`]   = (state, action) => ({ ...state, fetching: false, fetched: false, error: action.payload })
    })

    // Si le gestionnaire d'action est surchargé, utiliser cette version
    if (overrides.hasOwnProperty(action.type))
        return overrides[action.type](state, action)
    // Sinon s'il s'agit d'une action CRUD, utiliser celle de reducerMap
    if (reducerMap.hasOwnProperty(action.type))
        return reducerMap[action.type](state, action)
    // Sinon rien
    return state;
}
