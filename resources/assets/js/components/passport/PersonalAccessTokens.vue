<template>
    <div>
        <div class="card drop-shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h5><b>Tokens d'accès personnel</b></h5>
                    </div>

                    <div class="col-6 text-right">
                        <a class="btn btn-primary" tabindex="-1" @click="showCreateTokenForm">Créer un token</a>
                    </div>
                </div>

                <p class="mt-3 mb-0" v-if="tokens.length === 0">Vous n'avez pas encore crée de token d'accès personnel.</p>

                <!-- Personal Access Tokens -->
                <dl class="row mt-3 mb-0" v-if="tokens.length > 0" v-for="token in tokens">
                    <dt class="col-sm-3">
                        <span class="d-block mb-2">{{ token.name }}</span>
                    </dt>
                    <dd class="col-sm-9">
                        <button class="btn btn-primary btn-sm mb-1" tabindex="-1" @click="revoke(token)">
                            Supprimer
                        </button>
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Create Token Modal -->
        <div class="modal fade" id="modal-create-token" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <h4><b>Créer un token</b></h4>
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

                        <!-- Create Token Form -->
                        <form role="form" @submit.prevent="store">
                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Nom :</label>

                                <div class="col-md-9">
                                    <input id="create-token-name" type="text" class="form-control" name="name" v-model="form.name">
                                </div>
                            </div>

                            <!-- Scopes -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Scopes :</label>

                                <div class="col-md-9">
                                    <div v-for="(description, name) in scopes" v-if="name.startsWith('user')">
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
                                <button type="button" class="btn btn-primary" @click="store">Créer le token</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Access Token Modal -->
        <div class="modal fade" id="modal-access-token" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <h4><b>Token d'accès personnel</b></h4>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            </div>
                        </div>

                        <p>
                            Voici votre token d'accès personnel. C'est la seule fois qu'il sera affiché, ne le perdez pas ! Vous pouvez maintenant utiliser ce token pour faire des requêtes à l'API.
                        </p>

                        <div class="bg-light rounded-corners p-2 mb-3">
                            <code>{{ accessToken }}</code>
                        </div>

                        <!-- Modal Actions -->
                        <div class="row">
                            <div class="col-6 text-left">
                                
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
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
                accessToken: null,

                tokens: [],
                scopes: [],

                form: {
                    name: '',
                    scopes: [],
                    errors: []
                }
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
                this.getTokens();
                this.getScopes();

                $('#modal-create-token').on('shown.bs.modal', () => {
                    $('#create-token-name').focus();
                });
            },

            /**
             * Get all of the personal access tokens for the user.
             */
            getTokens() {
                axios.get('/oauth/personal-access-tokens')
                        .then(response => {
                            this.tokens = response.data;
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
             * Show the form for creating new tokens.
             */
            showCreateTokenForm() {
                $('#modal-create-token').modal('show');
            },

            /**
             * Create a new personal access token.
             */
            store() {
                this.accessToken = null;

                this.form.errors = [];

                axios.post('/oauth/personal-access-tokens', this.form)
                        .then(response => {
                            this.form.name = '';
                            this.form.scopes = [];
                            this.form.errors = [];

                            this.tokens.push(response.data.token);

                            this.showAccessToken(response.data.accessToken);
                        })
                        .catch(error => {
                            if (typeof error.response.data === 'object') {
                                this.form.errors = _.flatten(_.toArray(error.response.data.errors));
                            } else {
                                this.form.errors = ['Une erreur est survenue. Veuillez réessayer.'];
                            }
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
             * Show the given access token to the user.
             */
            showAccessToken(accessToken) {
                $('#modal-create-token').modal('hide');

                this.accessToken = accessToken;

                $('#modal-access-token').modal('show');
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                axios.delete('/oauth/personal-access-tokens/' + token.id)
                        .then(response => {
                            this.getTokens();
                        });
            }
        }
    }
</script>
