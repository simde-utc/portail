/**
 * Création et gestion automatique des actions que l'on dispatch via redux
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
**/

// Liste de toutes les actions REST api possibles
export let actionsData = {
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
  get: (target, prop) => {
    // On crée la méthode de gestion de requête Axios
    const method = function(...args) {
      let id, queryParams, jsonData;
      
      // On match si c'est une méthode HTTP connue et on extrait les bons arguments
      switch (prop) {
        case 'find':
        case 'one':
        case 'get':
          if (args.length > 0 || target.idIsGiven || prop === 'one') {
            // One n'a pas besoin d'id
            if (target.idIsGiven || prop === 'one')
              [ queryParams, jsonData ] = args;
            else {
              [ id, queryParams, jsonData ] = args;
              target.addId(id);
            }
            return target.generateAction('get', queryParams, jsonData);
          }

        case 'all':
          [ queryParams, jsonData ] = args;
          return target.generateAction('all', queryParams, jsonData);

        case 'remove':
          prop = 'delete';
        case 'create':
        case 'update':
        case 'delete':
          if (target.idIsGiven || prop === 'create')
            [ queryParams, jsonData ] = args;
          else {
            [ id, queryParams, jsonData ] = args;
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

      // On arrive là quand prop n'est pas une requête directe
      // On retourne bien sûr un proxy sur sois-même pour se gérer de nouveau
      return new Proxy(target, actionHandler);
    };

    // HTTP - Si c'est une action HTTP, l'exécuter
    if (actionsData[prop] !== undefined) {
      return method;
    }
    // METHOD - Si c'est une méthode de l'objet Action, on l'exécute sans rochiner
    else if (target[prop] !== undefined) {
      return target[prop];
    }
    // STORE - Si on appelle une méthode qui agit directement sur la sauvegarde dans le store
    else if (prop === 'definePath') {
      // Change le chemin de sauvegarde dans le store store
      return (path) => {
        target.path = path.slice();
        target.pathLocked = true;

        return new Proxy(target, actionHandler);
      }
    } else if (prop === 'addValidStatus') {
      // Ajoute un status valide
      return (validStatus) => {
        target.validStatus.push(validStatus);

        return new Proxy(target, actionHandler);
      }
    } else if (prop === 'defineValidStatus') {
      // Défini les status valides
      return (validStatus) => {
        target.validStatus = validStatus;

        return new Proxy(target, actionHandler);
      }
    }
    // ELSE - Sinon, on ajoute la catégorie et on gère dynamiquement
    // si c'est un appel propriété/méthode (expliquer sur un article de mon blog)
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
  /**
   * Contruction de l'Action
   *
   * @param      {String}  rootUri  L'URI de base des requêtes Axios
   * @return     {Proxy}            Retourne un Proxy lié à l'object et non l'object directement !
   */
  constructor(rootUri) {
    this.rootUri = rootUri || '/api/v1';
    this.uri = '';
    this.idIsGiven = false;
    this.path = [];
    this.pathLocked = false;
    this.actions = actionsData;
    this.validStatus = [ 200, 201, 202, 203, 204 ];

    return new Proxy(this, actionHandler);
  }

  /** Ajoute une étape à l'URI */
  addUri(uri) {
    this.uri += '/' + uri;

    if (!this.pathLocked) {
      this.path.push(uri);
    }
  }

  /** Ajoute l'id à l'URI */
  addId(id) {
    this.uri += '/' + id;

    if (!this.pathLocked) {
      this.path.push(id);
      this.idIsGiven = true;
    }
  }

  /** Génère le type de l'action (ex: ALL_USER_PREFERENCE) */
  generateType(action) {
    return this.actions[action].type + this.path.join('_').toUpperCase();
  }

  /** Génère finalement l'action */
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
      payload: window.axios[actionData.method](this.generateUri(this.rootUri + this.uri, queryParams), jsonData)
    };
  }

  /** Génère l'URI en fonction des queries */
  generateUri(uri, queryParams) {
    const queries = this.generateQueries(queryParams);
    return uri + (queries.length === 0 ? '' : ('?' + queries));
  }

  /** Génère la query string selon la map queryParams */
  generateQueries(queryParams, prefix) {
    let queries = []
    for (const key in queryParams) {
      if (queryParams.hasOwnProperty(key)) {
        if (Object.is(queryParams[key]))
          queries.push(this.generateQuery(queryParams[key], true));
        else
          queries.push(encodeURIComponent(prefix ? ('[' + key + ']') : key) + '=' + encodeURIComponent(queryParams[key]));
      }
    }
    return queries.join('&');
  }
}

// On crée dynamiquement nos actions (chaque action est une nouvelle instance de la classe Actions)
// Appelable: actions.category1 || actions('rootUri').category1
const actions = new Proxy(rootUri => new Actions(rootUri), {
  get: (target, prop) => (new Actions())[prop]
});

export default actions;
