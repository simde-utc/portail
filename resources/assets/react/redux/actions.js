/**
 * Création et gestion automatique des actions que l'on dispatch via redux
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license AGPL-3.0
**/

// Liste de toutes les actions REST api possibles
export const actionsData = {
  all: {
    type: 'GET_ALL_',
    method: 'get',
    action: 'updateAll',
    affectsAll: true,
  },
  get: {
    type: 'GET_ONE_',
    method: 'get',
    action: 'update',
    affectsAll: false,
  },
  create: {
    type: 'CREATE_',
    method: 'post',
    action: 'insert',
    affectsAll: false,
  },
  update: {
    type: 'UPDATE_',
    method: 'put',
    action: 'update',
    affectsAll: false,
  },
  delete: {
    type: 'DELETE_',
    method: 'delete',
    action: 'delete',
    affectsAll: false,
  },
};

// Gestionnaire d'actions (crée dynamiquement les routes api à appeler et où stocker les données)
export const actionHandler = {
  get: (target, prop) => {
    // On crée la méthode de gestion de requête
    const method = function(...args) {
      var id, queryParams, jsonData;
      // On match si c'est une méthode HTTP connue et on wipe tout
      switch (prop) {
        case 'one':
        case 'get':
          if (args.length > 0 || target.idIsGiven || prop === 'one') {
            if (target.idIsGiven || prop === 'one')
              [ queryParams, jsonData ] = args;
            else {
              [ id, queryParams, jsonData ] = args;

              target.addId(id);
            }

            return target.generateAction('get', queryParams, jsonData);
            break;
          }

        case 'all':
          [ queryParams, jsonData ] = args;

          return target.generateAction('all', queryParams, jsonData);
          break;

        case 'create':
        case 'update':
        case 'remove':
          if (target.idIsGiven)
            [ queryParams, jsonData ] = args;
          else {
            [ id, queryParams, jsonData ] = args;

            target.addUri(id);
          }

          return target.generateAction(prop, queryParams, jsonData);
          break;

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
    else if (target[prop] !== undefined) {
      return target[prop];
    }
    // On ajoute la catégorie et on gère dynmaiquement si c'est un appel propriété/méthode (expliquer sur un article de mon blog)
    else {
      target.addUri(prop);
      target.idIsGiven = false;

      return new Proxy(method, {
        get: (target, prop) => target()[prop]
      });
    }
  }
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

    return new Proxy(this, actionHandler);
  }

  addUri(uri) {
    if (this.pathLocked)
      throw 'Can not set defined path'
    else {
      this.uri += '/' + uri
      this.path.push(uri)
    }
  }

  addId(id) {
    if (this.pathLocked)
      throw 'Can not set defined path'
    else {
      this.uri += '/' + id
      this.path.push('id:' + id)
      this.idIsGiven = true;
    }
  }

  setPath(path) {
    if (this.pathLocked)
      throw 'Can not set defined path'
    else
      this.path = path;
  }

  definePath(path) {
    this.path = path;
    this.pathLocked = true;
  }

  generateType(action) {
    return this.actions[action].type + this.path.join('_').toUpperCase();
  }

  generateAction(action, queryParams = {}, jsonData = {}) {
    var actionData = this.actions[action];

    return {
      type: this.generateType(action),
      meta: { affectsAll: actionData.affectsAll, arrayAction: actionData.action, path: this.path, timestamp: Date.now() },
      payload: window.axios[actionData.method](this.generateUri(this.rootUri + this.uri, queryParams), jsonData)
    };
  }

  generateUri(uri, queryParams) {
    var queries = this.generateQueries(queryParams)

    return uri + (queries.length === 0 ? '' : ('?' + queries))
  }

  generateQueries(queryParams, prefix) {
    var queries = []

    for (var key in queryParams) {
      if (queryParams.hasOwnProperty(key)) {
        if (typeof queryParams[key] === 'string')
          queries.push(encodeURIComponent(prefix ? ('[' + key + ']') : key) + '=' + encodeURIComponent(queryParams[key]))
        else
          queries.push(this.generateQuery(queryParams[key], true))
      }
    }

    return queries.join('&')
  }
}

// On crée dynamiquement nos actions (chaque action est une nouvelle génération de la classe)
// Appelable: actions.category1 || actions('rootUri').category1
const actions = new Proxy(rootUri => new Actions(rootUri), {
  get: (target, prop) => {
    return (new Actions())[prop]
  }
});

export default actions;
