<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        OAuth Clients
                    </span>

                    <a class="action-link" tabindex="-1" @click="showCreateClientForm">
                        Create New Client
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Current Clients -->
                <p class="mb-0" v-if="clients.length === 0">
                    You have not created any OAuth clients.
                </p>

                <table class="table table-borderless mb-0" v-if="clients.length > 0">
                    <thead>
                        <tr>
                            <th>Client ID</th>
                            <th>Name</th>
							<th>Asso id</th>
                            <th>Secret</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="client in clients">
                            <!-- ID -->
                            <td style="vertical-align: middle;">
                                {{ client.id }}
                            </td>

                            <!-- Name -->
                            <td style="vertical-align: middle;">
                                {{ client.name }}
                            </td>

                            <!-- Asso id -->
                            <td style="vertical-align: middle;">
                                {{ client.asso_id }}
                            </td>

                            <!-- Secret -->
                            <td style="vertical-align: middle;">
                                <code>{{ client.secret }}</code>
                            </td>

                            <!-- See Button -->
                            <td style="vertical-align: middle;">
                                <a class="action-link" tabindex="-1" @click="see(client)">
                                    Voir
                                </a>
                            </td>

                            <!-- See Button -->
                            <td style="vertical-align: middle;">
                                <a class="action-link" tabindex="-1" @click="edit(client)">
                                    Modifier
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Client Modal -->
        <div class="modal fade" id="modal-create-client" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Create Client
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="form.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
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
                                <label class="col-md-3 col-form-label">Name</label>

                                <div class="col-md-9">
                                    <input id="create-client-name" type="text" class="form-control"
                                                                @keyup.enter="store" v-model="form.name">

                                    <span class="form-text text-muted">
                                        Something your users will recognize and trust.
                                    </span>
                                </div>
                            </div>

                            <!-- Asso -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Asso id</label>

                                <div class="col-md-9">
                                    <input name="asso_id" type="number" min="0" class="form-control"
                                                                @keyup.enter="store" v-model="form.asso_id">

                                    <span class="form-text text-muted">
										L'id de l'asso a qui créer la clé
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Redirect URL</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="redirect"
                                                    @keyup.enter="store" v-model="form.redirect">

                                    <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                                </div>
                            </div>

                            <!-- Scopes -->
                            <div class="form-group">
                                <label class="col-md-4 col-form-label">Scopes</label>

								<div class="col-md-12">
                                    <div v-for="(description, name) in scopes" v-if="name.startsWith('client')">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                    @click="toggleScope(name)"
                                                    :checked="scopeIsAssigned(name)">

                                                    {{ description }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>

                        <button type="button" class="btn btn-primary" @click="store">
                            Créer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Client Modal -->
        <div class="modal fade" id="modal-see-client" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Voir
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="form.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
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
                                <label class="col-md-3 col-form-label">Name</label>

                                <div class="col-md-9">
                                    <input id="edit-client-name" type="text" disabled class="form-control"
                                                                @keyup.enter="update" v-model="form.name">

                                    <span class="form-text text-muted">
                                        Something your users will recognize and trust.
                                    </span>
                                </div>
                            </div>

                            <!-- Asso -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Asso id</label>

                                <div class="col-md-9">
                                    <input type="number" min="0" disabled class="form-control"
                                                                @keyup.enter="update" v-model="form.asso_id">

                                    <span class="form-text text-muted">
                                        L'id de l'asso a qui créer la clé
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Redirect URL</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control" disabled name="redirect"
                                                    @keyup.enter="update" v-model="form.redirect">

                                    <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                                </div>
                            </div>

							<!-- Scopes -->
                            <div class="form-group" v-if="form.scopes.length > 0"
														@keyup.enter="update" v-model="form.scopes">
                                <label class="col-md-4 col-form-label">Scopes</label>

                                <div class="col-md-9">
                                    <ul v-for="scope in form.scopes">
                                        <li>
                                            {{ scopes[scope] }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Client Modal -->
        <div class="modal fade" id="modal-edit-client" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Modifier
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="form.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
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
                                <label class="col-md-3 col-form-label">Name</label>

                                <div class="col-md-9">
                                    <input id="edit-client-name" type="text" class="form-control"
                                                                @keyup.enter="update" v-model="form.name">

                                    <span class="form-text text-muted">
                                        Something your users will recognize and trust.
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Redirect URL</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="redirect"
                                                    @keyup.enter="update" v-model="form.redirect">

                                    <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                                </div>
                            </div>

							<!-- Scopes -->
                            <div class="form-group">
                                <label class="col-md-4 col-form-label">Scopes</label>

								<div class="col-md-12">
                                    <div v-for="(description, name) in scopes" v-if="name.startsWith('client')">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                    @click="toggleScope(name)"
                                                    :checked="scopeIsAssigned(name)">

                                                    {{ description }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
						<button type="button" class="btn btn-urgent" data-dismiss="modal" @click="destroy">Supprimer</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="update">Modifier</button>
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
                            form.errors = ['Something went wrong. Please try again.'];
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
