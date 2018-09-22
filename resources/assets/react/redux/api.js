const handler = {
  get: (target, prop) => {
    return (...args) => {
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
          target.addUri(prop);
          target.idIsGiven = false;

          // On ajoute l'id s'il est renseigné
          if (args.length === 1) {
            target.addId(args[0]);
          }

          break;
      }

      return new Proxy(target, handler);
    };
  }
};

export class Actions {
  constructor(rootUri) {
    this.rootUri = rootUri || '/api/v1';
    this.uri = '';
    this.idIsGiven = false;
    this.path = [];
    this.pathLocked = false;
    this.actions = {
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

    return new Proxy(this, handler);
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

  generateUri(uri, uriParams) {
    var queries = this.generateQueries(this.queryParams)

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

export const actions = (rootUri) => new Actions(rootUri);
export default actions;
