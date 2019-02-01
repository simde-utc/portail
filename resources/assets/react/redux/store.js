/**
 * Création et gestion automatique et dynmaique du store géré par redux (store refait sur la base du travail d'Alexandre)
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
**/

import produce from 'immer';
import { applyMiddleware, createStore } from 'redux';

// Import Middlewares
import thunk from 'redux-thunk';
import promise from 'redux-promise-middleware';
import { createLogger } from 'redux-logger';

// Suffixes des actions asynchrones
export const ASYNC_SUFFIXES = {
	loading: 'LOADING',
	success: 'SUCCESS',
	error: 'ERROR'
}

// Configure Middlewares
export const middlewares = applyMiddleware(
	thunk,
	promise({
		promiseTypeSuffixes: Object.values(ASYNC_SUFFIXES)
	}),
	// createLogger({ collapse: true })
);

// State de base de chaque ressource (CRUD)
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

/**
 * La racine du store, version superman
 * Elle contient la racine du store à proprement parler 'resources' qui contient l'arbre des ressources
 * Par exemple:
 * 	resources
 * 		- user
 * 			- details
 * 			- preferences
 * 		- assos
 * 			- users...
 * Le reste c'est que des fonctions trop stylax
 */
export const storeRoot = {
	resources: {},

	// Converti une route uri (string) en array | ex: 'assos/calendars' => ['assos', 'calendars']
	pathToSteps: function (props, ...additions) {
		if (typeof props === 'string')
			return props.split('/').concat(additions);
		return Array.isArray(props) ? props.concat(additions) : additions;
	},

	/**
	 * Permet d'accéder facilement à un élément du store
	 *
	 * @param  {String}  path              Le chemin séparé par des '/'
	 * @param  {Any}     replacement       La valeur à retourner si le chemin ne mène à rien
	 * @param  {Boolean} forceReplacement  Renvoyer remplacement si la valeur atteinte est un object vide
	 * return  {Any}                       La valeur atteinte ou remplacement
	 */
	get: function (path, replacement = {}, forceReplacement = false) {
		let data = this;
		const steps = this.pathToSteps(path);

		for (const key in steps) {
			if (data[steps[key]] !== undefined) {
				data = data[steps[key]]
			} else if (data.resources[steps[key]] !== undefined) {
				data = data.resources[steps[key]]
			} else {
				return replacement;
			}
		}

		if (forceReplacement && (data instanceof Object) && Object.keys(data).length === 0)
			return replacement;
		return data;
	},

	/** Retrouver un élément précis */
	getData: function (path, replacement = [], forceReplacement = true) {
		return this.get(this.pathToSteps(path, 'data'), replacement, forceReplacement);
	},

	findData: function (path, value, key = 'id', replacement = {}, forceReplacement = true) {
		// Les ressources sont rangées par id:
		if (key === 'id') {
			const steps = this.pathToSteps(path, 'value');
			return this.getData(steps, replacement, forceReplacement);
		} else {
			const data = this.getData(path, []);
			for (const k in data) {
				if (data[k][key] === value) {
					if (!forceReplacement || !(data[k] instanceof Object) || Object.keys(data[k]).length > 0) {
						return data[k];
					}
				}
			}
		}
		return replacement;
	},
	getError: function (path, replacement = null, forceReplacement = true) {
		return this.get(this.pathToSteps(path, 'error'), replacement, forceReplacement);
	},
	hasFailed: function (path, replacement = false, forceReplacement = true) {
		return this.get(this.pathToSteps(path, 'failed'), replacement, forceReplacement);
	},
	getStatus: function (path, replacement = null, forceReplacement = true) {
		return this.get(this.pathToSteps(path, 'status'), replacement, forceReplacement);
	},
	getLastUpdate: function (path, replacement = null, forceReplacement = true) {
		return this.get(this.pathToSteps(path, 'lastUpdate'), replacement, forceReplacement);
	},
	isFetching: function (path, replacement = false, forceReplacement = true) {
		return this.get(this.pathToSteps(path, 'fetching'), replacement, forceReplacement);
	},
	isFetched: function (path, replacement = false, forceReplacement = true) {
		return this.get(this.pathToSteps(path, 'fetched'), replacement, forceReplacement);
	},
	// Permet de savoir si une requête s'est terminée
	hasFinished: function (path, replacement = false, forceReplacement = true) {
		return this.get(this.pathToSteps(path, 'fetched'), replacement, forceReplacement) ||
			this.get(this.pathToSteps(path, 'failed'), replacement, forceReplacement);
	},
};

/** Simple fonction pour faire une deep copy d'un object */
export function clone(destination, source) {
	// Comme le JS ne fait pas deep copy avec Object.assign, on est obligé de le faire nous-même..
	for (const key in source) {
		if (source.hasOwnProperty(key)) {
			if (source[key] instanceof Object) {
				destination[key] = Array.isArray(source[key]) ? [] : {};
				clone(destination[key], source[key]);
			}	else {
				destination[key] = source[key];
			}
		}
	}
	return destination;
}

// Ici, toute la magie opère, on génère dynamiquement et automatiquement la route api et l'emplacement dans le store
export function buildStorePath(store, path) {
	let place = store;
	let part;

	for (const key in path) {
		part = path[key];

		// Étend l'arbre 'resources' pour 'part' à partir d'une copie d'initialCrudState
		if (place.resources[part] === undefined) {
			place.resources[part] = {};
			clone(place.resources[part], initialCrudState);
		}

		place = place.resources[part];
	}

	return place;
}

export function makeResourceSuccessed(place, timestamp, status) {
	place.fetching = false;
	place.fetched = true;
	place.error = null;
	place.failed = false;
	place.lastUpdate = timestamp;
	place.status = status;
}

// Ici on crée le store et on modifie ses données via immer en fonction de la récup des données
function storeReducer(state = storeRoot, action) {
	console.debug(action.type); // DEBUG
	
	if (action.meta && action.meta.path && action.meta.path.length > 0) {
		return produce(state, draft => {
			let path = action.meta.path;
			let id;

			// Si on ne modifie qu'une donnée précise, il faut qu'on change le statut pour la ressource
			if (action.meta.action !== 'updateAll') {
				path = path.slice();
				id = path.pop();
			}

			let place = buildStorePath(draft, path);
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
								let element = data[key];
								let placeForData = buildStorePath(draft, path.concat([element.id]));

								makeResourceSuccessed(placeForData, timestamp, status);
								placeForData.data = element;
							}
						}
						else if (data.id) {
							let placeForData = buildStorePath(draft, path.concat([data.id]));

							makeResourceSuccessed(placeForData, timestamp, status);
							placeForData.data = data;
						}
						else {
							let keys = Object.keys(data);

							for (let key in keys) {
								let element = data[keys[key]];
								let placeForData = buildStorePath(draft, path.concat([keys[key]]));

								makeResourceSuccessed(placeForData, timestamp, status);
								placeForData.data = element;
							}
						}
					}
					else {
						// On stock la data dans la liste des données de la ressource
						let index;
						switch (action.meta.action) {
							case 'update':
								index = place.data.findIndex(dataFromPlace => dataFromPlace.id === data.id);
								if (index > -1) {
									place.data[index] = data;
									break;
								}

							case 'create':
								place.data.push(data);
								break;

							case 'delete':
								index = place.data.findIndex(dataFromPlace => dataFromPlace.id === data.id);
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
								let placeForData = buildStorePath(draft, path.concat([id]));

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
									let placeForIdData = placeForData;
									let placeForData = buildStorePath(draft, path.concat([data.id]));

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
				let place = buildStorePath(draft, path);

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
}

export default createStore(storeReducer, middlewares);
