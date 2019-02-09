/**
 * Création et gestion automatique des actions que l'on dispatch via redux
 * 
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 * */

// Liste de toutes les actions REST api possibles
export const actionsData = {
	all: {
		type: 'ALL_',
		method: 'get',
		action: 'updateAll',
	},
	find: {
		type: 'FIND_',
		method: 'get',
		action: 'update',
	},
	create: {
		type: 'CREATE_',
		method: 'post',
		action: 'insert',
	},
	update: {
		type: 'UPDATE_',
		method: 'put',
		action: 'update',
	},
	remove: {
		type: 'DELETE_',
		method: 'delete',
		action: 'delete',
	},
	delete: {
		type: 'DELETE_',
		method: 'delete',
		action: 'delete',
	},
};

// On crée des alias:
actionsData.one = actionsData.find;
actionsData.get = actionsData.find;
actionsData.remove = actionsData.delete;

// Gestionnaire d'actions (crée dynamiquement les routes api à appeler et où stocker les données)
export const actionHandler = {
	get: (_target, prop) => {

		let target = _target;

		// On crée la méthode de gestion de requête
		const method = (...args) => {
			let id;
			let queryParams;
			let jsonData;
			// On match si c'est une méthode HTTP connue et on wipe tout
			switch (prop) {
				case 'find':
				case 'one':
				case 'get':
					if (args.length > 0 || target.idIsGiven || prop === 'one') {
						if (target.idIsGiven || prop === 'one') [queryParams, jsonData] = args;
						else {
							[id, queryParams, jsonData] = args;

							target.addId(id);
						}

						return target.generateAction('get', queryParams, jsonData);
					}

				case 'all':
					[queryParams, jsonData] = args;

					return target.generateAction('all', queryParams, jsonData);

				case 'create':
				case 'update':
				case 'remove':
				case 'delete':
					if (target.idIsGiven || prop === 'create') [queryParams, jsonData] = args;
					else {
						[id, queryParams, jsonData] = args;

						target.addUri(id);
					}

					return target.generateAction(prop, queryParams, jsonData);

				default:
					// On ajoute l'id s'il est renseigné
					if (args.length === 1) {
						target.addId(args[0]);
					}

					break;
			}

			// On retourne bien sûr un proxy sur sois-même pour se gérer de nouveau
			return new Proxy(target, actionHandler);
		};

		// Si c'est une action HTTP, l'exécuter
		if (Object.keys(actionsData).includes(prop)) {
			return method;
		}
		// Si c'est une méthode de l'objet Action, on l'exécute sans rochiner
		if (target[prop] !== undefined) {
			return target[prop];
		}
		// Si on appelle une méthode qui agit directement sur la sauvegarde dans le store
		if (prop === 'definePath') {
			return path => {
				target.path = path.slice();
				target.pathLocked = true;

				return new Proxy(target, actionHandler);
			};
		}
		// Si on appelle une méthode qui agit directement sur la sauvegarde dans le store
		if (prop === 'addValidStatus') {
			return validStatus => {
				target.validStatus.push(validStatus);

				return new Proxy(target, actionHandler);
			};
		}
		// Si on appelle une méthode qui agit directement sur la sauvegarde dans le store
		if (prop === 'defineValidStatus') {
			return validStatus => {
				target.validStatus = validStatus;

				return new Proxy(target, actionHandler);
			};
		}
		// On ajoute la catégorie et on gère dynmaiquement si c'est un appel propriété/méthode (expliquer sur un article de mon blog)

		target.addUri(prop);
		target.idIsGiven = false;

		return new Proxy(method, {
			get: (func, key) => func()[key],
		});
	},
};

// Classe de gestion des actions (génération automatique des routes et création des appels HTTP)
export class Actions {
	constructor(rootUri) {
		this.rootUri = rootUri || '/api/v1';
		this.uri = '';
		this.idIsGiven = false;
		this.path = [];
		this.pathLocked = false;
		this.actions = actionsData;
		this.validStatus = [200, 201, 202, 203, 204];

		return new Proxy(this, actionHandler);
	}

	addUri(uri) {
		this.uri += `/${uri}`;

		if (!this.pathLocked) {
			this.path.push(uri);
		}
	}

	addId(id) {
		this.uri += `/${id}`;

		if (!this.pathLocked) {
			this.path.push(id);
			this.idIsGiven = true;
		}
	}

	generateType(action) {
		return this.actions[action].type + this.path.join('_').toUpperCase();
	}

	generateAction(action, queryParams = {}, jsonData = {}) {
		const actionData = this.actions[action];

		return {
			type: this.generateType(action),
			meta: {
				action: actionData.action,
				validStatus: this.validStatus,
				path: this.path,
				timestamp: Date.now(),
			},
			payload: window.axios[actionData.method](
				this.generateUri(this.rootUri + this.uri, queryParams),
				jsonData
			),
		};
	}

	generateUri(uri, queryParams) {
		const queries = this.generateQueries(queryParams);

		return uri + (queries.length === 0 ? '' : `?${queries}`);
	}

	generateQueries(queryParams, prefix) {
		const queries = [];

		for (const key in queryParams) {
			if (queryParams.hasOwnProperty(key)) {
				const value = queryParams[key];

				if (value !== undefined) {
					if (Object.is(value)) queries.push(this.generateQuery(value, true));
					else
						queries.push(
							`${encodeURIComponent(prefix ? `[${key}]` : key)}=${encodeURIComponent(value)}`
						);
				}
			}
		}

		return queries.join('&');
	}
}

// On crée dynamiquement nos actions (chaque action est une nouvelle génération de la classe)
// Appelable: actions.category1 || actions('rootUri').category1
const actions = new Proxy(rootUri => new Actions(rootUri), {
	get: (target, prop) => {
		return new Actions()[prop];
	},
});

export default actions;
