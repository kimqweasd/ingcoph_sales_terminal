<template>
    <v-app>
        <v-container class="d-flex justify-center" style="height: 100%;">

            <v-dialog v-model="sync.dialog" scrollable persistent max-width="580px" style="">
                <v-card>
                    <v-row class="m-0">
                        <v-card-title v-text="sync.title"></v-card-title>
                    </v-row>

                    <v-card-text style="max-height: 560px; padding: 5px; border:1px solid #e0e0e0;">
                        <div v-for="(masterDataModule, index) in masterDataModules">
                            <div class="d-flex ml-2" style="margin-bottom: 1px;">
                                <v-progress-linear
                                    v-if="masterDataModule.processing"
                                    indeterminate
                                    color="blue"
                                ></v-progress-linear>
                            </div>
                            <div class="d-flex justify-space-between mb-5">
                                <div class="ml-2">
                                    <div class="text-md font-weight-bold" v-text="masterDataModule.list.title"></div>
                                    <div class="text-sm font-light" v-text="masterDataModule.list.subTitle"></div>
                                </div>
                                <div>
                                    <v-btn :loading="masterDataModule.processing" :disabled="masterDataModule.disabled" v-on:click="syncModule(masterDataModule.slug)" tile small color="primary">Sync</v-btn>
                                </div>
                            </div>
                        </div>
                    </v-card-text>

                    <v-card-actions class="d-flex justify-end">
                        <v-btn v-if="!loading.state && !syncingAnyModule" small v-on:click="sync.dialog = false">Cancel</v-btn>
                        <v-btn :loading="loading.state || syncingAnyModule" small v-on:click="syncModule('*')">Sync All</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-card tile outlined elevation="" width="450" class="my-auto">
                <v-progress-linear
                    v-if="loading.state"
                    indeterminate
                    color="green"
                ></v-progress-linear>
                <v-row class="m-0">
                    <v-card-title v-if="user.name">{{`Hello ${user.name}`}}</v-card-title>
                </v-row>
                <v-card-text>
                    <div class="col-sm-12 col-md-12">
                        <div v-for="error in errors" class="red--text" v-bind:key="error" v-text="error"></div>
                        <div v-for="message in messages" class="blue--text" v-bind:key="message" v-text="message"></div>
                    </div>
                </v-card-text>
                <v-card-actions class="d-flex justify-end">
                    <v-btn v-if="!loading.state" v-on:click="goToSalesTerminal()">SALES TERMINAL</v-btn>
                    <v-btn :loading="loading.state" v-on:click="sync.dialog = true">SYNC MASTER DATA</v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </v-app>
</template>

<script>
export default {
    name: "Sync",

    data() {
        return {
            loading: {
                state: false,
            },
            user: {
                name: null
            },
            masterDataModules: [
                {
                    slug: 'store',
                    processing: false,
                    list: {
                        title: 'Store Information',
                        subTitle: 'Sync Store Details and Settings'
                    },
                    disabled: false
                },
                {
                    slug: 'items',
                    processing: false,
                    list: {
                        title: 'Items',
                        subTitle: 'Sync Store Items and Inventory'
                    },
                    disabled: false
                },
                {
                    slug: 'promos',
                    processing: false,
                    list: {
                        title: 'Promos',
                        subTitle: 'Sync Store Promos'
                    },
                    disabled: true
                }
            ],
            message: {
                value: '',
                dialog: false
            },
            sync: {
                title: "Sync POS Terminal with Store Master Data",
                dialog: false,
            },
            errors: [],
            messages: []
        }
    },

    computed: {
        syncingAnyModule: function(){
            return this.masterDataModules.filter(module => module.processing).length > 0;
        }
    },

    mounted() {
        let that = this;

        if (_.isEmpty(shared.user)) {
            that.syncUser();
        } else {
            that.user.name = shared.user.name;
        }
    },

    methods: {
        syncModule(args){
            let that = this;

            that.loading.state = true;

            window.salesTerminalAxios.post('sync', {
                data: args.data,
                module: args.module
            }).then((response) => {

                console.log(response);

                that.loading.state = false;

            }).catch((error) => {
                that.errors = ['Sync Failed'];
                console.log(error.response.data);
                that.loading.state = false;
            });
        },

        syncUser(){
            let that = this;

            that.loading.state = true;

            that.api[that.auth.api].account().then((response) => {

                that.user.name = response.data.user.name;

                that.syncModule({
                    module: 'user',
                    data: response.data.user
                })

            }).catch((error) => {
                that.errors = ['User Sync Failed'];
                console.log(error.response.data);
                that.loading.state = false;
            });
        },

        goToSalesTerminal(){
            window.location = "/";
        }
    }
}
</script>

<style scoped>

</style>
