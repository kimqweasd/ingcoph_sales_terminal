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
            <v-dialog v-model="sync.dialog" scrollable persistent max-width="780px" style="">
                <v-card>
                    <v-row class="m-0">
                        <v-card-title v-text="sync.title"></v-card-title>
                    </v-row>

                    <v-card-text style="max-height: 560px; padding: 5px; border:1px solid #e0e0e0;">
                        <div v-for="(masterDataModule, index) in masterDataModules.filter(module => module.independent === false)">
                            <div class="d-flex" style="margin-bottom: 1px;">
                                <v-progress-linear
                                    v-if="masterDataModule.processing"
                                    indeterminate
                                    color="blue"
                                ></v-progress-linear>
                            </div>
                            <v-row no-gutters align="center" class="mb-3">
                                <v-col cols="12" sm="6">
                                    <div>
                                        <div class="text-md font-weight-bold" v-text="masterDataModule.list.title"></div>
                                        <div class="text-sm font-light overline" v-text="masterDataModule.list.subTitle"></div>
                                    </div>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-row no-gutters align="center">
                                        <v-btn
                                            v-on:click="initializeSync(masterDataModule.slug, masterDataModule.serviceUrl, masterDataModule.paginated, masterDataModules.indexOf(masterDataModule))"
                                            :loading="masterDataModule.processing"
                                            :disabled="masterDataModule.disabled"
                                            tile small color="primary"><v-icon>mdi mdi-download</v-icon>&nbsp;Sync</v-btn>
                                        <span>
                                            <!--<span class="ml-2 text-md font-weight-bold" v-text="`MASTER DATA : ${masterDataModule.count.saved} / ${masterDataModule.count.source}`"></span>-->
                                            <span class="ml-2 text-md font-weight-bold" v-text="`MASTER DATA : `"></span>

                                            <span class="text-md font-weight-bold"><animated-number :value="masterDataModule.count.saved" :formatValue="formatToNoneDecimal" :duration="700"></animated-number></span>
                                            <span class="text-md font-weight-bold" v-text="` / `"></span>
                                            <span class="text-md font-weight-bold"><animated-number :value="masterDataModule.count.source" :formatValue="formatToNoneDecimal" :duration="700"></animated-number></span>
                                        </span>
                                    </v-row>
                                </v-col>
                            </v-row>
                        </div>
                        <div>
                            <div v-for="error in errors" class="red--text" v-bind:key="error" v-text="error"></div>
                            <div v-for="message in messages" class="blue--text" v-bind:key="message" v-text="message"></div>
                        </div>
                    </v-card-text>

                    <v-card-actions class="d-flex justify-end">
                        <v-btn v-if="!loading.state && !syncingAnyModule" small outlined tile text v-on:click="sync.dialog = false">CLOSE</v-btn>
                        <v-btn :loading="loading.state || syncingAnyModule" small outlined tile text v-on:click="syncAllMasterData()">SYNC ALL</v-btn>
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
                                       :disabled="loading.state || loggingOut || !store.loaded"
                                       tile outlined small text>SYNC MASTER DATA</v-btn>

                                <v-btn v-on:click="goToSalesTerminal()"
                                       :disabled="loading.state || loggingOut || !store.loaded"
                                       tile outlined small text>SALES TERMINAL</v-btn>
                            </v-col>
                        </v-row>
                    </div>
                    <div v-if="!sync.dialog" v-for="error in errors" class="red--text" v-bind:key="error" v-text="error"></div>
                    <div v-if="!sync.dialog" v-for="message in messages" class="blue--text" v-bind:key="message" v-text="message"></div>
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
                id: null,
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
            messages: [],
            lastSyncActionIsSingle: true
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
                paginateBy: 25,
                serviceUrl: that.apiInterface[auth.api].items(),
                processing: false,
                list: {
                    title: 'Items',
                    subTitle: 'Sync Store Items'
                },
                template: (item) => {
                    return Object.assign({},{
                        id: item.id,
                        model: item.item.model,
                        name: item.item.name,
                        quantity: item.quantity,
                        srp: item.store_price
                    });
                },
                independent: false,
                disabled: false,
                count: {
                    source: 0,
                    saved: shared.items.count
                }
            },
            {
                slug: 'promos',
                paginated: true,
                paginateBy: 1,
                serviceUrl: that.apiInterface[auth.api].promos(),
                processing: false,
                list: {
                    title: 'Promos',
                    subTitle: 'Sync Store Promos'
                },
                template: (promo) => {
                    return Object.assign({},{
                        'promo' : {
                            'id' : promo.id,
                            'name' : promo.name,
                            'type' : promo.type,
                            'spend' : promo.spend,
                            'discount_type' : promo.discount_type,
                            'discount_amount' : promo.discount_amount,
                            'all_items' : Boolean(promo.all_items),
                            'start_date' : Boolean(promo.all_items) ? Moment(promo.start_date).startOf('day').format(that.system.date.format) : null,
                            'end_date' : Boolean(promo.all_items) ? Moment(promo.end_date).endOf('day').format(that.system.date.format) : null,
                            'created_at' : Moment(promo.created_at).format(that.system.date.format),
                            'updated_at' : Moment(promo.updated_at).format(that.system.date.format),
                        },
                        'details': _.isEmpty(promo.promo_details) ? [] : promo.promo_details.reduce((haystack, needle)=>{
                            haystack.push({
                                'id' : needle.id,
                                'promo_id' : needle.promo_id,
                                'item_id' : needle.item_inventory_id,
                                'type' : needle.type,
                                'quantity' : needle.quantity,
                                'free_of_charge' : Boolean(needle.free_of_charge),
                                'created_at' : Moment(needle.created_at).format(that.system.date.format),
                                'updated_at' : Moment(needle.updated_at).format(that.system.date.format),
                            });
                            return haystack;
                        },[]),
                        'pivot': _.isEmpty(promo.item_inventories) ? [] : promo.item_inventories.reduce((haystack, needle)=>{
                            haystack.push({
                                'item_id': needle.pivot.item_inventory_id,
                                'promo_id': needle.pivot.promo_id,
                                'start_date': Moment(needle.pivot.start_date).startOf('day').format(that.system.date.format),
                                'end_date': Moment(needle.pivot.start_date).endOf('day').format(that.system.date.format),
                            });
                            return haystack;
                        },[])
                    });
                },
                independent: false,
                disabled: false,
                count: {
                    source: 0,
                    saved: shared.promos.count
                }
            },
            {
                slug: 'payment_methods',
                paginated: true,
                paginateBy: 2,
                serviceUrl: that.apiInterface[auth.api].paymentMethods(),
                processing: false,
                list: {
                    title: 'Payment Methods',
                    subTitle: 'Sync Store Payment Methods'
                },
                template: (item) => {
                    return Object.assign({},{
                        id: item.id,
                        name: item.name,
                        remarks: item.remarks,
                    });
                },
                independent: false,
                disabled: false,
                count: {
                    source: 0,
                    saved: shared.payment_methods.count
                }
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
            await console.log("Resolved store.");
        } else {
            that.store.id = shared.store.id;
            that.store.name = shared.store.name;
            that.store.address = shared.store.address;
            that.store.loaded = true;
        }
    },

    methods: {
        formatToNoneDecimal(value){
            return `${Number(value).toFixed(0)}`;
        },

        async syncAllMasterData(){
            let that = this;

            that.lastSyncActionIsSingle = false;

            if (_.isNull(that.store.id)) {
                return false;
            }

            that.masterDataModules.filter(module => (module.disabled === false && module.independent === false && module.paginated && !_.isEmpty(module.serviceUrl))).forEach(module => {
                let masterDataModulesIndex = that.masterDataModules.indexOf(module);
                that.toggleLoading(masterDataModulesIndex, true);
                that.iteratePagination(module.slug, module.serviceUrl, masterDataModulesIndex, {store_id :that.store.id, page: 1, perPage: module.paginateBy});
            });
        },

        async iteratePagination(module, serviceUrl, index, pagination) {
            let that = this;

            if(_.isNull(serviceUrl)){
                return false;
            }

            if (pagination.page === 1) {
                that.masterDataModules[index].count.saved = 0;
                that.messages.push(`[${that.log++}] Requesting ${module.replace('_', ' ')} from store hub server...`);
            }

            that.getFromApiService(module, serviceUrl, Object.assign({},{
                store_id : pagination.store_id,
                page: pagination.page,
                per_page: pagination.perPage
            })).then(async (response) => {
                //console.log([`${module} page ${pagination.page}`, response]);

                if (pagination.page === 1) {
                    that.messages.push(`[${that.log++}] Downloading ${module.replace('_', ' ')}...`);
                }

                that.masterDataModules[index].count.source = response.total;

                //console.log(`Syncing ${module} page ${pagination.page}`);

                await new Promise((resolve, reject) => {
                    let moduleTemplate = (needle) => {
                        return that.masterDataModules[index].template(needle);
                    }

                    let data = response.data.reduce((haystack, needle)=>{
                        haystack.push(moduleTemplate(needle));
                        //Another way for module templating
                        //haystack.push(that.masterDataModules[index].template(needle));
                        return haystack;
                    },[]);

                    that.syncPaginatedModule(module, {data: data, page: pagination.page}).then((response) => {
                        that.masterDataModules[index].count.saved = response.data.data.count;
                        //console.log(`Synced ${module} page ${pagination.page}`);
                        resolve();
                    }).catch((error) => {
                        that.catchError(error, ['Terminal service failed...'], index);
                    });
                });

                if (pagination.page !== response.last_page) {
                    await that.iteratePagination(module, serviceUrl, index, Object.assign({}, {
                        store_id: pagination.store_id,
                        page: pagination.page + 1,
                        perPage: pagination.perPage
                    }))
                } else {
                    console.log(`Finished iterating ${module}`);
                    that.toggleLoading(index, false);
                    that.errors = [];
                }
            }).catch((error) => {
                that.catchError(
                    error,
                    ['Authentication failed : token expired', 'Preparing Automatic Token Refresher', 'Please wait...'],
                    index,
                    ()=>{
                        return (that.lastSyncActionIsSingle)
                            ? that.initializeSync(module, serviceUrl, true, index)
                            : that.syncAllMasterData();
                    }
                );
            });
        },

        async initializeSync(module, serviceUrl, paginated, index = null){
            let that = this;

            that.lastSyncActionIsSingle = true;

            if (_.isNull(serviceUrl)) return false;

            that.toggleLoading(index, true);

            if(!paginated) {
                let data = null;
                let readyToSync = false;

                await that.getFromApiService(module, serviceUrl, paginated, index).then((response) => {
                    data = response;
                }).catch((error) => {
                    that.catchError(
                        error,
                        ['Authentication failed : token expired', 'Preparing Automatic Token Refresher', 'Please wait...'],
                        index,
                        ()=>{return that.initializeSync(module, serviceUrl, paginated, index)}
                    );
                });

                if (!that.errors.length) {
                    that.messages.push(`[${that.log++}] Request ${module.replace('_', ' ')} received`);
                    console.log(`Request ${module.replace('_', ' ')} received`);
                }

                if (data) {
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
                }

                if (!_.isNull(data) && readyToSync) {
                    await that.syncModule(module, data, index);
                }
            } else {
                console.log(arguments);
                await that.iteratePagination(module, serviceUrl, index, {store_id :that.store.id, page: 1, perPage: that.masterDataModules[index].paginateBy});
            }
        },

        getFromApiService(module, serviceUrl, params = {}){
            let that = this;

            if (_.isEmpty(params)) {
                that.messages.push(`[${that.log++}] Requesting ${module.replace('_', ' ')} from store hub server`);
                console.log(`Requesting ${module.replace('_', ' ')} from store hub server`);
            }

            return new Promise((resolve, reject) => {
                window[auth.api].get(serviceUrl, {
                    params: params
                }).then((response) => {
                    setTimeout(()=>{
                        resolve(response.data[module]);
                    },(_.isEmpty(params) ? 2000 : 0));
                }).catch((error) => {
                    reject(error);
                });
            });
        },

        syncPaginatedModule(module, payload){
            return new Promise((resolve, reject) => {
                window.salesTerminalAxios.post('sync', {
                    module: module,
                    data: payload.data,
                    page: payload.page
                }).then((response) => {
                    resolve(response);
                }).catch((error) => {
                    reject(error);
                });
            });
        },

        async syncModule(module, data, index){
            let that = this;

            that.messages.push(`[${that.log++}] Syncing ${module.replace('_', ' ')}...`);
            await console.log(`Syncing ${module.replace('_', ' ')}...`);

            await window.salesTerminalAxios.post('sync', {
                module: module,
                data: data
            }).then((response) => {

                that.toggleLoading(index, false);

                that.messages.push(`[${that.log++}] Synced ${module.replace('_', ' ')}`);
                console.log(`Synced ${module.replace('_', ' ')}`);

                that.sync.dialog = false;
                that.errors = [];

            }).catch((error) => {
                that.catchError(error, ['Terminal service failed...'], index);
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
            that.store.id = data.id;
            that.store.name = data.name;
            that.store.address = data.address;
            that.store.loaded = true;
            await console.log("Resolved Store.");

            //Start syncing master data, since store just got finished
            await new Promise(resolve => {
                that.sync.dialog = true;
                that.syncAllMasterData();
                that.errors = [];
                resolve();
            });
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
            window.location = "/sales-terminal";
        },

        logout(){
            this.errors = [];
            this.messages = [];
            this.loggingOut = true;
            window.location = "logout";
        },

        catchError(error, messages, loadingIndex, callback = null){
            let that = this;

            that.errors = messages;

            if (error.response) {
                // The request was made and the server responded with a status code
                // that falls out of the range of 2xx
                console.log(error.response.data.message);
                that.messages.push(`[${that.log++}] Initializing access token refresh...`);
                that.refreshToken({refresh_token : auth.refresh_token}, auth.api, callback);
            } else if (error.request) {
                // The request was made but no response was received
                that.errors = ['Failed to connect to store hub server...'];
            } else {
                that.errors = ['Something went wrong... Please contact your administrator...'];
                console.log('Something went wrong...', error.message);
            }

            if (loadingIndex === null){
                that.loading.state = false;
            } else {
                that.masterDataModules[loadingIndex].processing = false;
            }
        }
    },

    watch:{
        messages: {
            handler() {
                let that = this;

                setTimeout(()=>{
                    if (that.messages.length) {
                        that.messages.shift();
                    }
                }, 10000);
            }
        },
    }
}
</script>

<style scoped>
</style>
