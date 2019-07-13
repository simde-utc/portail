/**
 * Automatic and dynamic store management and creation by redux. (store remade on the basis of Alexandre's work)
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import produce from 'immer';
import { applyMiddleware, createStore, compose } from 'redux';

// Import Middlewares
import promise from 'redux-promise-middleware';
// import { createLogger } from 'redux-logger';
import thunk from 'redux-thunk';

// Asynchronous actions suffixes.
export const ASYNC_SUFFIXES = {
	loading: 'LOADING',
	success: 'SUCCESS',
	error: 'ERROR',
	config: 'CONFIG',
};

/**
 * ActionTypes Creator
 * Function that creates the CRUD action types.
 * @param      {string}   name    The name of the resource at the singular in uppercase.
 * @return     {Object}           A CRUD action types set for the ressource name.
 */
export const createCrudTypes = name => ({
	// _resource_name: name,
	getAll: `GET_ALL_${name}`,
	getOne: `GET_ONE_${name}`,
	create: `CREATE_${name}`,
	update: `UPDATE_${name}`,
	delete: `DELETE_${name}`,
});

// Configures Middlewares
let middlewares = applyMiddleware(
	thunk,
	promise({
		promiseTypeSuffixes: Object.values(ASYNC_SUFFIXES),
	})
	// createLogger({ collapse: true })
);

/* eslint-disable no-underscore-dangle */
if (process.env.NODE_ENV === 'development') {
	middlewares = (window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose)(middlewares);
}
/* eslint-enable */

// Store root.
export const store = {
	// Converts a URI in array | ex: 'assos/calendars' => ['assos', 'calendars']
	propsToArray(_props) {
		let props = _props;

		if (typeof props === 'string') {
			props = props.split('/');
		}

		if (!(props instanceof Array)) {
			return [];
		}

		return props;
	},
	// Finds easily a store's element.
	get(_props, replacement = {}, forceReplacement = false) {
		let data = this;
		const props = this.propsToArray(_props);

		for (let key = 0; key < props.length; key++) {
			if (data[props[key]] !== undefined) {
				data = data[props[key]];
			} else if (data.resources[props[key]] !== undefined) {
				data = data.resources[props[key]];
			} else {
				return replacement;
			}
		}

		if (forceReplacement && data instanceof Object && Object.keys(data).length === 0)
			return replacement;

		return data;
	},

	// Finds a precise element.
	getData(props, replacement = [], forceReplacement = true) {
		return this.get(this.propsToArray(props).concat(['data']), replacement, forceReplacement);
	},
	findData(props, value, key = 'id', replacement, forceReplacement = true) {
		// Resources are ordered by id.
		if (key === 'id') {
			return this.getData(
				this.propsToArray(props).concat(['value']),
				replacement,
				forceReplacement
			);
		}

		const data = this.getData(props, []);

		for (const k in data) {
			if (data[k][key] === value) {
				if (!forceReplacement || !(data[k] instanceof Object) || Object.keys(data[k]).length > 0) {
					return data[k];
				}
			}
		}

		return replacement;
	},
	getError(props, replacement = null, forceReplacement = true) {
		return this.get(this.propsToArray(props).concat(['error']), replacement, forceReplacement);
	},
	hasFailed(props, replacement = false, forceReplacement = true) {
		return this.get(this.propsToArray(props).concat(['failed']), replacement, forceReplacement);
	},
	getStatus(props, replacement = null, forceReplacement = true) {
		return this.get(this.propsToArray(props).concat(['status']), replacement, forceReplacement);
	},
	getLastUpdate(props, replacement = null, forceReplacement = true) {
		return this.get(this.propsToArray(props).concat(['lastUpdate']), replacement, forceReplacement);
	},
	isFetching(props, replacement = false, forceReplacement = true) {
		return this.get(this.propsToArray(props).concat(['fetching']), replacement, forceReplacement);
	},
	isFetched(props, replacement = false, forceReplacement = true) {
		return this.get(this.propsToArray(props).concat(['fetched']), replacement, forceReplacement);
	},
	// Allows to know if a request has finished.
	hasFinished(props, replacement = false, forceReplacement = true) {
		return (
			this.get(this.propsToArray(props).concat(['fetched']), replacement, forceReplacement) ||
			this.get(this.propsToArray(props).concat(['failed']), replacement, forceReplacement)
		);
	},
	resources: {},
	config: {},
};

// Root of every CRUD category.
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

// As the JS doesn't do a deep copy with Object.assign, it must be done.
export const initCrudState = (_state, initialState = initialCrudState) => {
	const state = _state;

	for (const key in initialState) {
		if (initialState.hasOwnProperty(key)) {
			if (initialState[key] instanceof Object) {
				state[key] = Array.isArray(initialState[key]) ? [] : {};
				initCrudState(state[key], initialState[key]);
			} else {
				state[key] = initialState[key];
			}
		}
	}

	return state;
};

// Here the magic works. It generates dnamically and automatically the api route and the place in the store.
export const buildStorePath = (_store, path) => {
	let place = _store;
	let part;

	for (const key in path) {
		part = path[key];

		if (place.resources[part] === undefined) {
			place.resources[part] = {};
			initCrudState(place.resources[part]);
		}

		place = place.resources[part];
	}

	return place;
};

export const makeResourceSuccessed = (_place, timestamp, status) => {
	const place = _place;

	place.fetching = false;
	place.fetched = true;
	place.error = null;
	place.failed = false;
	place.lastUpdate = timestamp;
	place.status = status;

	return place;
};

// Store creation and Store's data update through immer depending on the data retrievement.
export default createStore((state = store, action) => {
	if (action.type === ASYNC_SUFFIXES.config) {
		return produce(state, draft => {
			const keys = Object.keys(action.config);
			for (let i = 0; i < keys.length; i++) {
				const key = keys[i];

				draft.config[key] = action.config[key];
			}

			return draft;
		});
	}

	console.debug(action.type);

	if (action.meta && action.meta.path && action.meta.path.length > 0) {
		return produce(state, draft => {
			let { path } = action.meta;
			let id;

			// If a precise data is updated, the resource status must be updated.
			switch (action.meta.action) {
				case 'updateAll':
				case 'create':
				case 'insert':
					break;

				default:
					path = path.slice();
					id = path.pop();
			}

			let place = buildStorePath(draft, path);

			if (action.type.endsWith(`_${ASYNC_SUFFIXES.loading}`)) {
				place.fetching = true;
				place.status = null;
			}
			// If we defined the HTTP response as a valid response.
			else if (
				action.meta.validStatus.includes(action.payload.status || action.payload.response.status)
			) {
				if (action.type.endsWith(`_${ASYNC_SUFFIXES.success}`)) {
					const { timestamp, status, data } = action.payload;
					place = makeResourceSuccessed(place, timestamp, status);

					if (action.meta.action === 'updateAll') {
						place.data = data;

						if (Array.isArray(data)) {
							for (const key in data) {
								const element = data[key];
								let placeForData = buildStorePath(draft, path.concat([element.id]));

								placeForData = makeResourceSuccessed(placeForData, timestamp, status);
								placeForData.data = element;
							}
						} else if (data.id) {
							let placeForData = buildStorePath(draft, path.concat([data.id]));

							placeForData = makeResourceSuccessed(placeForData, timestamp, status);
							placeForData.data = data;
						} else {
							const keys = Object.keys(data);

							for (const key in keys) {
								const element = data[keys[key]];
								let placeForData = buildStorePath(draft, path.concat([keys[key]]));

								placeForData = makeResourceSuccessed(placeForData, timestamp, status);
								placeForData.data = element;
							}
						}
					} else {
						let index;

						// Stores the data in the resource's data list.
						switch (action.meta.action) {
							case 'update':
								index = place.data.findIndex(dataFromPlace => dataFromPlace.id === data.id);

								if (index > -1) {
									place.data[index] = data;

									break;
								}

							case 'insert':
							case 'create':
								place.data.push(data);

								break;

							default:
								// 'delete'.
								index = place.data.findIndex(dataFromPlace => dataFromPlace.id === data.id);

								if (index > -1) {
									place.data.splice(index, 1);
								}

								break;
						}

						// Store the data by id for the resource.
						if (id) {
							switch (action.meta.action) {
								case 'update': {
									// On modifie/stock la donnée via l'id
									let placeForData = buildStorePath(draft, path.concat([id]));

									placeForData = makeResourceSuccessed(placeForData, timestamp, status);
									placeForData.data = data;

									break;
								}
								default:
									// 'delete'
									delete place.resources[id];

									break;
							}
						}

						// For example if we have an assciation ant we look for it by login.
						if (id !== data.id) {
							// Store the data by id for the resource.
							switch (action.meta.action) {
								case 'update':
								case 'insert':
								case 'create': {
                  // Stores/updates data trough data's id.
                  
									let placeForData = buildStorePath(draft, path.concat([data.id]));
									const placeForIdData = placeForData;

									placeForData = makeResourceSuccessed(placeForData, timestamp, status);
									placeForData.data = data;
									placeForIdData.resources = placeForData.resources;

									break;
								}
								default:
									// 'delete'.
									delete place.resources[data.id];
									break;
							}
						}
					}
				} else if (action.type.endsWith(`_${ASYNC_SUFFIXES.error}`)) {
					if (id) {
						place = buildStorePath(draft, path.concat([id]));
					}

					place.data = [];
					place.fetching = false;
					place.fetched = true;
					place.error = action.payload;
					place.failed = false;
					place.status = action.payload.response.status;
				}
			} else if (action.type.endsWith(`_${ASYNC_SUFFIXES.error}`)) {
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
			// Redux success but on our side the HTTP code is refused.
			else {
				place = buildStorePath(draft, path);

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
