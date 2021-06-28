<template>
    <v-app>
        <v-container class="d-flex justify-center" style="height: 100%;">
            <v-card tile outlined width="450" class="my-auto">
                <v-progress-linear
                    v-if="loading.state"
                    indeterminate
                    color="green"
                ></v-progress-linear>
                <v-card-text>
                    <v-form @submit.prevent="authenticateAt('storeHub')" class="pa-2">
                        <div class="col-sm-12 col-md-12">
                            <input v-model="form.username" autocomplete="off" :disabled="loading.state" type="text" class="form-control" placeholder="Username">
                        </div>
                        <div class="col-sm-12 col-md-12">
                            <input v-model="form.password" autocomplete="off" :disabled="loading.state" type="password" class="form-control" placeholder="Password">
                        </div>
                        <div style="padding-left: 12px;">
                            <div v-for="error in errors" class="red--text" v-bind:key="error" v-text="error"></div>
                            <div v-for="message in messages" class="blue--text" v-bind:key="message" v-text="message"></div>
                        </div>

                        <div class="col-sm-12 col-md-12">
                            <v-btn @lick="authenticateAt('storeHub')" type="submit" :loading="loading.state" outlined text class="d-flex items-center"><span>Login</span></v-btn>
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
            loading: {
                state: true,
            },
            form: {
                username: '',
                password: ''
            },
            errors: [],
            messages: []
        }
    },

    mounted() {
        if (auth.access_token) {
            window.location.replace('/');
        } else {
            this.loading.state = false;
        }
    },

    methods: {
        authenticateAt(app) {
            let that = this;

            if(_.isEmpty(app) || that.loading.state) return false;

            that.loading.state = true;

            that.apiInterface[app].authenticate({
                api: app,
                form: that.form
            }).then((response) => {
                that.errors = [];
                that.messages = ['Please wait...'];
                that.sessionAuthentication({
                    ...response.data,
                    api: app
                });
            }).catch((error) => {
                that.errors = ['Login Failed'];
                console.log(error.response.data);
                that.loading.state = false;
            });
        },

        sessionAuthentication(data) {
            let that = this;

            that.errors = [];
            that.messages = [];

            window.salesTerminalAxios.post('login', {
                api: data.api,
                access_token: data.access_token
            }).then((response) => {
                that.messages = ['Please wait...'];
                setTimeout(()=>{
                    window.location.replace('utility');
                }, 1500);
            }).catch((error) => {
                that.errors = ['Login Failed'];
                console.log(error.response.data);
                that.loading.state = false;
            });
        }
    },

    computed: {},

    watch: {}
}
</script>

<style scoped>

</style>
