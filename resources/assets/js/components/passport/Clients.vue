<template>
    <div>
        <div class="card drop-shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h5><b>Clients OAuth</b></h5>
                    </div>

                    <div class="col-6 text-right">
                        <a class="btn btn-primary" tabindex="-1" @click="showCreateClientForm">Créer un client</a>
                    </div>
                </div>

                <p class="mt-3 mb-0" v-if="clients.length === 0">Vous n'avez pas encore crée de client OAuth.</p>

                <!-- OAuth Clients -->
                <dl class="row mt-3 mb-0" v-if="clients.length > 0" v-for="client in clients">
                    <dt class="col-sm-3">
                        <span class="d-block mb-2">{{ client.name }}</span> 
                        <button class="btn btn-primary btn-sm mb-1" tabindex="-1" @click="see(client)">
                            Voir
                        </button>
                        <button class="btn btn-primary btn-sm mb-1" tabindex="-1" @click="edit(client)">
                            Modifier
                        </button>
                    </dt>
                    <dd class="col-sm-9">
                        ID Client : {{ client.id }} <br/>
                        ID Asso : {{ client.asso_id }} <br />
                        Secret : <code>{{ client.secret }}</code> <br />
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Create Client Modal -->
        <div class="modal fade" id="modal-create-client" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <h4><b>Créer un client</b></h4>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            </div>
                        </div>

                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="form.errors.length > 0">
                            <p class="mb-0"><strong>Erreur</strong></p>
                            <br>
                            <ul>
                                <li v-for="error in form.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Create Client Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Nom :</label>

                                <div class="col-md-9">
                                    <input id="create-client-name" type="text" class="form-control" @keyup.enter="store" v-model="form.name">

                                    <span class="form-text text-muted">Le nom qui s'affichera pour vos utilisateurs.</span>
                                </div>
                            </div>

                            <!-- Asso -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">ID Asso :</label>

                                <div class="col-md-9">
                                    <input name="asso_id" type="number" min="0" class="form-control" @keyup.enter="store" v-model="form.asso_id">

                                    <span class="form-text text-muted">
										L'ID de l'asso pour qui la clé est créee.
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Redirection :</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="redirect" value="http://example.com" @keyup.enter="store" v-model="form.redirect">

                                    <span class="form-text text-muted">
                                        Adresse de redirection après authentification.
                                    </span>
                                </div>
                            </div>

                            <!-- Scopes -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Scopes :</label>

								<div class="col-md-9">
                                    <div v-for="(description, name) in scopes" v-if="name.startsWith('client')">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                    @click="toggleScope(name)"
                                                    :checked="scopeIsAssigned(name)">

                                                &nbsp;

                                                <span data-toggle="tooltip" data-placement="right" :title="description">{{ name }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Modal Actions -->
                        <div class="row">
                            <div class="col-6 text-left">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Annuler</button>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" class="btn btn-primary" @click="store">Créer le client</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Client Modal -->
        <div class="modal fade" id="modal-see-client" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <h4><b>Voir</b></h4>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            </div>
                        </div>

                        <!-- Edit Client Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Nom :</label>

                                <div class="col-md-9">
                                    <input id="edit-client-name" type="text" disabled class="form-control"
                                                                @keyup.enter="update" v-model="form.name">

                                    <span class="form-text text-muted">Le nom qui s'affichera pour vos utilisateurs.</span>
                                </div>
                            </div>

                            <!-- Asso -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">ID Asso :</label>

                                <div class="col-md-9">
                                    <input type="number" min="0" disabled class="form-control"
                                                                @keyup.enter="update" v-model="form.asso_id">

                                    <span class="form-text text-muted">L'ID de l'asso pour qui la clé est créee.</span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Redirection :</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control" disabled name="redirect"
                                                    @keyup.enter="update" v-model="form.redirect">

                                    <span class="form-text text-muted">Adresse de redirection après authentification.</span>
                                </div>
                            </div>

							<!-- Scopes -->
                            <div class="form-group row" v-if="form.scopes.length > 0" @keyup.enter="update" v-model="form.scopes">
                                <label class="col-md-3 col-form-label">Scopes :</label>

                                <div class="col-md-9">
                                    <span class="d-block mb-1" v-for="scope in form.scopes">
                                        <code>{{ scope }}</code> : {{ scopes[scope] }}
                                    </span>
                                </div>
                            </div>
                        </form>

                        <div class="row">
                            <div class="col-12 text-right">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Client Modal -->
        <div class="modal fade" id="modal-edit-client" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <h4><b>Modifier un client</b></h4>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            </div>
                        </div>

                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="form.errors.length > 0">
                            <p class="mb-0"><strong>Erreur</strong></p>
                            <br>
                            <ul>
                                <li v-for="error in form.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Edit Client Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Nom :</label>

                                <div class="col-md-9">
                                    <input id="edit-client-name" type="text" class="form-control"
                                                                @keyup.enter="update" v-model="form.name">

                                    <span class="form-text text-muted">Le nom qui s'affichera pour vos utilisateurs.</span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Redirection :</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="redirect"
                                                    @keyup.enter="update" v-model="form.redirect">

                                    <span class="form-text text-muted">Adresse de redirection après authentification.</span>
                                </div>
                            </div>

							<!-- Scopes -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Scopes :</label>

								<div class="col-md-9">
                                    <div v-for="(description, name) in scopes" v-if="name.startsWith('client')">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                    @click="toggleScope(name)"
                                                    :checked="scopeIsAssigned(name)">

                                                    &nbsp;

                                                    <span data-toggle="tooltip" data-placement="right" :title="description">{{ name }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Modal Actions -->
                        <div class="row">
                            <div class="col-6 text-left">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Annuler</button>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" class="btn btn-danger mr-2" data-dismiss="modal" @click="destroy">Supprimer</button>
                                <button type="button" class="btn btn-primary" @click="store">Modifier le client</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                clients: [],
				client: {},

				scopes: [],

                form: {
                    errors: [],
					name: '',
                    asso_id: 1,
                    redirect: '',
					scopes: []
                },
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getClients();
				this.getScopes();

                $('#modal-create-client').on('shown.bs.modal', () => {
                    $('#create-client-name').focus();
                });

                $('#modal-edit-client').on('shown.bs.modal', () => {
                    $('#edit-client-name').focus();
                });
            },

            /**
             * Get all of the OAuth clients for the user.
             */
            getClients() {
                axios.get('/oauth/clients')
                        .then(response => {
                            this.clients = response.data;
                        });
            },

			/**
			 * Get all of the available scopes.
			 */
			getScopes() {
				axios.get('/oauth/scopes')
						.then(response => {
							this.scopes = response.data;
						});
			},

            /**
             * Toggle the given scope in the list of assigned scopes.
             */
            toggleScope(scope) {
                if (this.scopeIsAssigned(scope)) {
                    this.form.scopes = _.reject(this.form.scopes, s => s == scope);
                } else {
                    this.form.scopes.push(scope);
                }
            },

            /**
             * Determine if the given scope has been assigned to the token.
             */
            scopeIsAssigned(scope) {
                return _.indexOf(this.form.scopes, scope) >= 0;
            },

            /**
             * Show the form for creating new clients.
             */
            showCreateClientForm() {
				this.form = {
                    errors: [],
					name: '',
                    asso_id: 1,
                    redirect: '',
					scopes: []
                };

                $('#modal-create-client').modal('show');
            },

            /**
             * Create a new OAuth client for the user.
             */
            store() {
                this.persistClient(
                    'post', '/oauth/clients',
                    this.form, '#modal-create-client'
                );
            },

            /**
             * See the given client.
             */
            see(client) {
                this.form.id = client.id;
				this.form.name = client.name;
                this.form.asso_id = client.asso_id;
				this.form.redirect = client.redirect;

				try {
					this.form.scopes = JSON.parse(client.scopes);

					if (this.form.scopes === null)
						this.form.scopes = [];
				}
				catch (error) {
					this.form.scopes = [];
				}

                $('#modal-see-client').modal('show');
            },

            /**
             * Edit the given client.
             */
            edit(client) {
				this.client = client;
                this.form.id = client.id;
				this.form.name = client.name;
                this.form.asso_id = client.asso_id;
				this.form.redirect = client.redirect;

				try{
					this.form.scopes = JSON.parse(client.scopes);

					if (this.form.scopes === null)
						this.form.scopes = [];
				}
				catch (error){
					this.form.scopes = [];
				}

                $('#modal-edit-client').modal('show');
            },

            /**
             * Update the client being edited.
             */
            update() {
                this.persistClient(
                    'put', '/oauth/clients/' + this.form.id,
                    this.form, '#modal-edit-client'
                );
            },

            /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                axios[method](uri, form)
                    .then(response => {
                        this.getClients();

                        form.name = '';
                        form.redirect = '';
                        form.errors = [];

                        $(modal).modal('hide');
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data.errors));
                        } else {
                            form.errors = ['Une erreur est survenue. Veuillez réessayer'];
                        }
                    });
            },

            /**
             * Destroy the given client.
             */
            destroy() {
                axios.delete('/oauth/clients/' + this.client.id)
                        .then(response => {
                            this.getClients();
                        });
            }
        }
    }
</script>
