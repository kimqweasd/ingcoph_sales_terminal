<template>
    <v-app>
        <v-container class="d-flex justify-center" style="height: 100%;">

            <!--STORE SELECTION DIALOG -->
            <v-dialog v-model="storeSelection.dialog" scrollable persistent max-width="560px">
                <v-card>
                    <v-row class="m-0">
                        <v-card-title>
                            <span class="text-lg font-semibold" v-text="storeSelection.title"></span>
                        </v-card-title>
                    </v-row>

                    <v-card-text style="max-height: 560px; border:1px solid #e0e0e0; padding: 0;">
                        <v-data-table
                            v-model="storeSelection.selected"
                            :search="storeSelection.datatable.search"
                            :headers="storeSelection.datatable.headers"
                            :items="storeSelection.data"
                            :single-select="true"
                            item-key="id"
                            show-select
                            dense
                            disable-pagination
                            hide-default-footer
                            style="border:1px solid #e0e0e0; margin: 0;">
                        </v-data-table>
                    </v-card-text>

                    <v-card-actions class="d-flex justify-end">
                        <v-btn small :disabled="!storeSelection.selected.length" v-on:click="storeSelected()"><i class="fas fa-store mr-1"></i>SELECT</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- SYNC DIALOG -->
            <v-dialog v-model="sync.dialog" scrollable persistent max-width="580px" style="">
                <v-card>
                    <v-row class="m-0">
                        <v-card-title v-text="sync.title"></v-card-title>
                    </v-row>

                    <v-card-text style="max-height: 560px; padding: 5px; border:1px solid #e0e0e0;">
                        <div v-for="(masterDataModule, index) in masterDataModules.filter(module => module.independent === false)">
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
                                        v-on:click="initializeSync(masterDataModule.slug, masterDataModule.serviceUrl, masterDataModule.paginated, index)"
                                        :loading="masterDataModule.processing"
                                        :disabled="masterDataModule.disabled"
                                        tile small color="primary">Sync</v-btn>
                                </div>
                            </div>
                        </div>
                    </v-card-text>

                    <v-card-actions class="d-flex justify-end">
                        <v-btn v-if="!loading.state && !syncingAnyModule" small v-on:click="sync.dialog = false">CLOSE</v-btn>
                        <v-btn :loading="loading.state || syncingAnyModule" small v-on:click="initializeSync('*', null)">SYNC ALL</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-card tile outlined  class="my-auto">
                <v-progress-linear
                    v-if="loading.state"
                    indeterminate
                    color="green"
                ></v-progress-linear>
                <v-row class="m-0">
                    <v-card-title v-if="!user.loaded" v-text="`Please wait.`"></v-card-title>
                    <v-card-title v-if="user.loaded">{{`Hello ${user.name}`}}</v-card-title>
                </v-row>
                <v-card-text>
                    <div
                        v-for="(masterDataModule, index) in masterDataModules.filter(module => module.independent === true)"
                        class="d-flex">
                        <v-row align="center">
                            <v-col cols="12">
                                <div v-if="store.loaded">
                                    <div class="text-md" v-text="store.name"></div>
                                    <div v-text="store.address"></div>
                                </div>
                                <div v-else>
                                    <div class="text-md" v-text="'Syncing Store Details and Settings...'"></div>
                                </div>
                            </v-col>
                        </v-row>
                        <v-divider class="mx-4 my-0" vertical></v-divider>
                        <v-row align="center">
                            <v-col cols="12">
                                <v-btn
                                    v-on:click="initializeSync(masterDataModule.slug, masterDataModule.serviceUrl, masterDataModule.paginated, null)"
                                    :loading="loading.state"
                                    :disabled="masterDataModule.disabled || loggingOut"
                                    tile small color="primary">{{`Sync ${masterDataModule.slug}`}}</v-btn>

                                <v-btn v-on:click="sync.dialog = true"
                                   :disabled="loading.state || loggingOut"
                                   tile outlined small text>SYNC MASTER DATA</v-btn>

                                <v-btn v-on:click="goToSalesTerminal()"
                                   :disabled="loading.state || loggingOut"
                                   tile outlined small text>SALES TERMINAL</v-btn>
                            </v-col>
                        </v-row>
                    </div>
                    <div v-for="error in errors" class="red--text" v-bind:key="error" v-text="error"></div>
                    <div v-for="message in messages" class="blue--text" v-bind:key="message" v-text="message"></div>
                </v-card-text>
                <v-card-actions class="d-flex justify-end">
                    <div v-if="loggingOut" v-text="'Logging out... please wait...'"></div>
                    <v-btn outlined text v-if="!loading.state && !loggingOut" v-on:click="logout()">LOGOUT</v-btn>
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
            storeSelection: {
                title: 'Select Store',
                dialog: false,
                datatable : {
                    search: '',
                    headers: [
                        { text: 'NAME', value: 'name' },
                        { text: 'ADDRESS', value: 'address' }
                    ],
                },
                data: [],
                selected: []
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

    async mounted() {
        let that = this;

        that.loading.state = true;

        that.masterDataModules = [
            {
                slug: 'store',
                paginated: false,
                serviceUrl: that.apiInterface[auth.api].storeInfo(),
                processing: false,
                list: {
                    title: 'Store Information',
                    subTitle: 'Sync Store Details and Settings'
                },
                independent: true,
                disabled: false
            },
            {
                slug: 'items',
                paginated: true,
                serviceUrl: null,
                processing: false,
                list: {
                    title: 'Items',
                    subTitle: 'Sync Store Items and Inventory'
                },
                independent: false,
                disabled: true
            },
            {
                slug: 'promos',
                paginated: true,
                serviceUrl: null,
                processing: false,
                list: {
                    title: 'Promos',
                    subTitle: 'Sync Store Promos'
                },
                independent: false,
                disabled: true
            },
            {
                slug: 'payment_methods',
                paginated: true,
                serviceUrl: null,
                processing: false,
                list: {
                    title: 'Payment Methods',
                    subTitle: 'Sync Store Payment Methods'
                },
                independent: false,
                disabled: true
            }
        ];

        if (_.isEmpty(shared['user'])) {
            await that.initializeSync('user', that.apiInterface[auth.api].account(), false);
            await console.log("Resolved User.");
        } else {
            that.user.name = shared.user.name;
            that.user.loaded = true;
            that.loading.state = false;
        }

        if (_.isEmpty(shared['store'])) {
            await that.initializeSync('store', that.apiInterface[auth.api].storeInfo(), false, null);
        } else {
            that.store.name = shared.store.name;
            that.store.address = shared.store.address;
            that.store.loaded = true;
        }
    },

    methods: {
        async initializeSync(module, serviceUrl, paginated, index = null){
            let that = this;

            if (_.isEmpty(serviceUrl)) return false;

            that.toggleLoading(index, true);

            if(!paginated) {
                let data = null;
                let readyToSync = false;

                await that.getFromApiService(module, serviceUrl, paginated, index).then((response) => {
                    data = response;
                }).catch((error) => {
                    that.catchError(error, ['Sync Failed'], index);
                });

                await console.log("Response finish after 4 sec.");

                switch (module) {
                    case 'user':
                        that.user.name = data.name;
                        that.user.loaded = true;
                        readyToSync = true;
                        break;
                    case 'store':
                        that.storeSelection.data = data;
                        that.storeSelection.dialog = true;
                        break;
                }

                if (!_.isEmpty(data) && readyToSync) {
                    await that.syncModule(module, data, index);
                }
            }
        },

        getFromApiService(module, serviceUrl, paginated, index){
            let that = this;

            return new Promise((resolve, reject) => {
                window[auth.api].get(serviceUrl).then((response) => {
                    console.log("Delaying response of 2 sec.");

                    setTimeout(()=>{
                        resolve(response.data[module]);
                    },2000);
                }).catch((error) => {
                    reject(error);
                });
            });
        },

        async syncModule(module, data, index){
            let that = this;

            await console.log(`Syncing ${module}.`);

            await window.salesTerminalAxios.post('sync', {
                module: module,
                data: data
            }).then((response) => {

                that.toggleLoading(index, false);

                console.log(`Syncing ${module} Finished.`);

                that.sync.dialog = false;

            }).catch((error) => {
                that.catchError(error, ['Sync Failed'], index);
            });
        },

        async storeSelected(){
            let that = this;

            let data = null;

            data = that.storeSelection.selected[0];
            that.storeSelection.dialog = false;
            that.storeSelection.selected = [];

            let index = that.sync.dialog ? 0 : null;

            await that.syncModule('store', data, index);
            that.store.name = data.name;
            that.store.address = data.address;
            that.store.loaded = true;
            await console.log("Resolved Store.");
        },

        toggleLoading(index, state){
            let that = this;

            if (index === null){
                that.loading.state = state;
            } else {
                that.masterDataModules[index].processing = state;
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
