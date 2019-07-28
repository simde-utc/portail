/**
 * Actions dispatched through redux creation and management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

// List all posible REST actions.
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

// Aliasses creation.
actionsData.one = actionsData.find;
actionsData.get = actionsData.find;
actionsData.remove = actionsData.delete;

// Action Handler (Creates dynamically api routes to call and where to store the data).
export const actionHandler = {
	get: (_target, prop) => {
		const target = _target;

		// Request hangler method creation.
		const method = (...args) => {
			let id;
			let queryParams;
			let jsonData;
			// Matche if it's a known HTTP method and wipe everything.
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
					// If known, adds the ID.
					if (args.length === 1) {
						target.addId(args[0]);
					}

					break;
			}

			// Return a proxy on itself to handle itself again.
			return new Proxy(target, actionHandler);
		};

		// If it's an HTTP action, executes it.
		if (Object.keys(actionsData).includes(prop)) {
			return method;
		}

		// If it's an `Action` object method, executes it.
		if (target[prop] !== undefined) {
			return target[prop];
		}

		// If a method wich acts directly on the save in the store.
		if (prop === 'definePath') {
			return path => {
				target.path = path.slice();
				target.pathLocked = true;

				return new Proxy(target, actionHandler);
			};
		}

		// If a method wich acts directly on the save in the store.
		if (prop === 'addValidStatus') {
			return validStatus => {
				target.validStatus.push(validStatus);

				return new Proxy(target, actionHandler);
			};
		}

		// If a method wich acts directly on the save in the store.
		if (prop === 'defineValidStatus') {
			return validStatus => {
				target.validStatus = validStatus;

				return new Proxy(target, actionHandler);
			};
		}
		// The category is added and we handle dynamically if it's a property/method call.*

		target.addUri(prop);
		target.idIsGiven = false;

		return new Proxy(method, {
			get: (func, key) => func()[key],
		});
	},
};

// Actions management class (Automatic routes generation and HTTP call creation).
export class Actions {
	constructor(rootUri) {
		this.rootUri = rootUri || '/api/v1';
		this.uri = '';
		this.idIsGiven = false;
		this.path = [];
		this.pathLocked = false;
		this.actions = actionsData;
		this.validStatus = [200, 201, 202, 203, 204, 416];

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

// Actions are created dynamically (each action is a new class ganeration).
// callable: actions.category1 || actions('rootUri').category1
const actions = new Proxy(rootUri => new Actions(rootUri), {
	get: (target, prop) => {
		if (prop === 'config') {
			return modifications => {
				return {
					type: 'CONFIG',
					config: modifications,
				};
			};
		}

		return new Actions()[prop];
	},
});

export default actions;
