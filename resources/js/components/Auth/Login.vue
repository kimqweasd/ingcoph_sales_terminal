<template>
    <v-app>
        <v-container class="d-flex justify-center" style="height: 100%;">
            <v-card outlined width="450" class="my-auto">
                <v-progress-linear
                    v-if="loading.state"
                    indeterminate
                    color="green"
                ></v-progress-linear>
                <v-card-text>
                    <v-form class="pa-2">
                        <div class="col-sm-12 col-md-12">
                            <input v-model="form.username" autocomplete="off" type="text" class="form-control" placeholder="Username">
                        </div>
                        <div class="col-sm-12 col-md-12">
                            <input v-model="form.password" autocomplete="off" type="password" class="form-control" placeholder="Password">
                        </div>

                        <div class="col-sm-12 col-md-12">
                            <v-btn outlined text class="d-flex items-center"><span>Login</span></v-btn>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-container>
    </v-app>
</template>

<script>
export default {
    name: "Login",

    data() {
        return {
            form: {
                username: '',
                password: ''
            },
            loading: {
                state: false,
            }
        }
    },

    mounted() {
    },

    methods: {
        login() {
            let that = this;
        },

        redirect(response) {
            let that = this;
        },

        resetMessage() {
            let that = this;
        }
    },

    computed: {},

    watch: {}
}
</script>

<style scoped>

</style>