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
                                    <v-btn
                                        v-on:click="syncModule(masterDataModule.slug, masterDataModule.serviceUrl, masterDataModule.single, index)"
                                        :loading="masterDataModule.processing"
                                        :disabled="masterDataModule.disabled"
                                        tile small color="primary">Sync</v-btn>
                                </div>
                            </div>
                        </div>
                    </v-card-text>

                    <v-card-actions class="d-flex justify-end">
                        <v-btn v-if="!loading.state && !syncingAnyModule" small v-on:click="sync.dialog = false">CLOSE</v-btn>
                        <v-btn :loading="loading.state || syncingAnyModule" small v-on:click="syncModule('*', null)">SYNC ALL</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-card tile outlined width="450" class="my-auto">
                <v-progress-linear
                    v-if="loading.state"
                    indeterminate
                    color="green"
                ></v-progress-linear>
                <v-row class="m-0">
                    <v-card-title v-if="user.loaded">{{`Hello ${user.name}`}}</v-card-title>
                </v-row>
                <v-card-text>
                    <div v-if="store.loaded">
                        <div v-text="store.name"></div>
                        <div v-text="store.address"></div>
                    </div>
                    <v-divider></v-divider>
                    <div v-for="error in errors" class="red--text" v-bind:key="error" v-text="error"></div>
                    <div v-for="message in messages" class="blue--text" v-bind:key="message" v-text="message"></div>
                </v-card-text>
                <v-card-actions class="d-flex justify-end">
                    <div v-if="loggingOut" v-text="'Logging out... please wait...'"></div>
                    <v-btn outlined text v-if="!loading.state && !loggingOut" v-on:click="logout()">LOGOUT</v-btn>
                    <v-btn outlined text v-if="!loading.state && !loggingOut" v-on:click="goToSalesTerminal()">SALES TERMINAL</v-btn>
                    <v-btn outlined text v-if="!loggingOut" :loading="loading.state" v-on:click="sync.dialog = true">SYNC MASTER DATA</v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </v-app>
</template>

<script>
export default {
    name: "Utility",

    data() {
        return {
            loggingOut: false,
            loading: {
                state: false,
            },
            user: {
                loaded: false,
                name: '',
            },
            store: {
                loaded: false,
                name: '',
                address: '',
            },
            masterDataModules: [],
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

        that.loading.state = true;

        if (_.isEmpty(shared['user'])) {
            that.syncModule('user', that.apiInterface[auth.api].account(), true);
        } else {
            that.user.name = shared.user.name;
            that.user.loaded = true;

            that.loading.state = false;
        }

        if (!_.isEmpty(shared['store'])) {
            that.store.name = shared.store.name;
            that.store.address = shared.store.address;

            that.store.loaded = true;
        }

        that.masterDataModules = [
            {
                slug: 'store',
                single: true,
                serviceUrl: that.apiInterface[auth.api].storeInfo(),
                processing: false,
                list: {
                    title: 'Store Information',
                    subTitle: 'Sync Store Details and Settings'
                },
                disabled: false
            },
            {
                slug: 'items',
                single: false,
                serviceUrl: null,
                processing: false,
                list: {
                    title: 'Items',
                    subTitle: 'Sync Store Items and Inventory'
                },
                disabled: true
            },
            {
                slug: 'promos',
                single: false,
                serviceUrl: null,
                processing: false,
                list: {
                    title: 'Promos',
                    subTitle: 'Sync Store Promos'
                },
                disabled: true
            },
            {
                slug: 'payment_methods',
                single: false,
                serviceUrl: null,
                processing: false,
                list: {
                    title: 'Payment Methods',
                    subTitle: 'Sync Store Payment Methods'
                },
                disabled: true
            }
        ];
    },

    methods: {
        syncModule(module, serviceUrl, single, index = null){
            let that = this;

            if (_.isEmpty(serviceUrl)) return false;

            if (index === null){
                that.loading.state = true;
            } else {
                that.masterDataModules[index].processing = true;
            }

            // If module only has 1 row/record like [User, Store info data]
            if(single) {

                window[auth.api].get(serviceUrl).then((response) => {

                    switch (module) {
                        case 'user':
                            that.user.name = response.data.user.name;
                            that.user.loaded = true;
                            break;
                        case 'store':
                            that.store.name = response.data.store.name;
                            that.store.address = response.data.store.address;
                            that.store.loaded = true;
                            break;
                    }

                    window.salesTerminalAxios.post('sync', {
                        module: module,
                        data: response.data[module]
                    }).then((response) => {

                        if (index === null){
                            that.loading.state = false;
                        } else {
                            that.masterDataModules[index].processing = false;
                        }

                        that.sync.dialog = false;

                    }).catch((error) => { that.catchError(error, ['Sync Failed'], index);});
                }).catch((error) => {that.catchError(error, ['Sync Failed'], index);});
            }
        },

        goToSalesTerminal(){
            window.location = "/";
        },

        logout(){
            this.loggingOut = true;
            window.location = "logout";
        },

        catchError(error, messages, loadingIndex){
            let that = this;

            that.errors = messages;
            console.log(error.response.data);
            if (loadingIndex === null){
                that.loading.state = false;
            } else {
                that.masterDataModules[loadingIndex].processing = false;
            }
        }
    }
}
</script>

<style scoped>

</style>
