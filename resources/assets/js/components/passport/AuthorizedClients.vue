<template>
    <div v-if="tokens.length > 0">
        <div class="card drop-shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h5><b>Applications autorisées</b></h5>
                    </div>
                </div>

                <!-- Authorized Tokens -->
                <div class="row mt-3 mb-0" v-if="tokens.length > 0" v-for="token in tokens">
                    <div class="col-sm-3 mb-2">
                        <b>{{ token.client.name }}</b>
                    </div>

                    <div class="col-sm-6 mb-2">
                        <span v-if="token.scopes.length > 0">
                            {{ token.scopes.join(', ') }}
                        </span>
                    </div>

                    <div class="col-sm-3 text-md-right">
                        <a class="btn btn-primary btn-sm" @click="revoke(token)">
                            Révoquer
                        </a>
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
                tokens: []
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
             * Prepare the component (Vue 2.x).
             */
            prepareComponent() {
                this.getTokens();
            },

            /**
             * Get all of the authorized tokens for the user.
             */
            getTokens() {
                axios.get('/oauth/tokens')
                        .then(response => {
                            this.tokens = response.data;
                        });
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                axios.delete('/oauth/tokens/' + token.id)
                        .then(response => {
                            this.getTokens();
                        });
            }
        }
    }
</script>
