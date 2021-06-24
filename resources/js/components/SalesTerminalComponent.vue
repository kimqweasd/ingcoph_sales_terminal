<template>
    <v-app>
        <div style="height: 100vh; width: 100vw;background-color: white;">

            <v-row justify="center">

                <v-dialog v-model="terminal.message.dialog" scrollable persistent max-width="460px" style="">
                    <v-card>
                        <v-row class="m-0">
                            <v-card-title>
                                <span class="text-lg font-semibold" v-text="terminal.message.value"></span>
                            </v-card-title>
                            <v-col cols="pull-right-10 p-2">
                                <v-btn text icon color="gray" class="float-right" @click="terminal.message.dialog = false; terminal.settings.focus = true;">
                                    <v-icon>fas fa-times-circle</v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-dialog>

                <v-dialog v-model="customerSelection.dialog" scrollable persistent max-width="1260px" style="">
                    <v-card>
                        <v-row class="m-0">
                            <v-card-title>
                                <span class="text-lg font-semibold" v-text="customerSelection.settings.title"></span>
                            </v-card-title>
                            <v-col cols="pull-right-10 p-2">
                                <v-btn text icon color="gray" class="float-right" @click="customerSelection.dialog = false; terminal.settings.focus = true;">
                                    <v-icon>fas fa-times-circle</v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                        <div class="d-flex">
                            <v-btn small v-on:click="" class="m-2">
                                <i class="fas fa-list-alt mr-1"></i>Filters
                            </v-btn>
                            <v-spacer></v-spacer>
                            <v-text-field
                                v-model="customerSelection.datatable.search"
                                append-icon="mdi-magnify"
                                label="Search"
                                single-line
                                dense
                                hide-details
                            ></v-text-field>
                        </div>
                        <v-card-text style="max-height: 560px; border:1px solid #e0e0e0; padding: 0;">
                            <v-data-table
                                v-model="customerSelection.selected"
                                :search="customerSelection.datatable.search"
                                :headers="customerSelection.datatable.headers"
                                :items="customerSelection.data"
                                :single-select="customerSelection.settings.selection.singleSelect"
                                item-key="id"
                                show-select
                                dense
                                style="border:1px solid #e0e0e0; margin: 0;">
                            </v-data-table>
                        </v-card-text>

                        <v-card-actions class="d-flex justify-end">
                            <v-btn v-on:click="addCustomerSelection()"><i class="fas fa-plus mr-1"></i>Select</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>

                <v-dialog v-model="itemSelection.dialog" scrollable persistent max-width="1260px" style="">
                    <v-card>
                        <v-row class="m-0">
                            <v-card-title>
                                <span class="text-lg font-semibold" v-text="itemSelection.settings.title"></span>
                            </v-card-title>
                            <v-col cols="pull-right-10 p-2">
                                <v-btn text icon color="gray" class="float-right" @click="itemSelection.dialog = false; terminal.settings.focus = true;">
                                    <v-icon>fas fa-times-circle</v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                        <div class="d-flex">
                            <v-btn small v-on:click="" class="m-2">
                                <i class="fas fa-list-alt mr-1"></i>Filters
                            </v-btn>
                            <v-spacer></v-spacer>
                            <v-text-field
                                v-model="itemSelection.datatable.search"
                                append-icon="mdi-magnify"
                                label="Search"
                                single-line
                                dense
                                hide-details
                            ></v-text-field>
                        </div>
                        <v-card-text style="max-height: 560px; border:1px solid #e0e0e0; padding: 0;">
                            <v-data-table
                                v-model="itemSelection.selected"
                                :search="itemSelection.datatable.search"
                                :headers="itemSelection.datatable.headers"
                                :items="itemSelection.data"
                                :single-select="itemSelection.settings.selection.singleSelect"
                                item-key="model"
                                show-select
                                dense
                                style="border:1px solid #e0e0e0; margin: 0;">
                            </v-data-table>
                        </v-card-text>

                        <v-card-actions class="d-flex justify-end">
                            <v-btn v-on:click="addItemSelection()"><i class="fas fa-cart-plus mr-1"></i>Select</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-row>

            <div style="height: 86vh; width: 100%; border: 1px solid silver;">

                <v-row no-gutters style="height:100%;">
                    <v-col cols="12" sm="8">
                        <div style="height:16vh; width:100%;">
                            <v-row no-gutters style="height:100%;width:100%;border-right: 1px solid silver;">
                                HEADER
                            </v-row>
                        </div>
                        <div style="border-top: 1px solid silver;border-right: 1px solid silver; height:63vh; width:100%;">
                            <v-row no-gutters style="height:100%;width:100%;">
                                <div id="terminalItemsOverflow" style="overflow-y: scroll;width: inherit; height: auto; max-height: -webkit-fill-available;">
                                    <v-simple-table id="terminalItems" style="border:1px solid #e0e0e0; ">
                                        <template v-slot:default>
                                            <thead>
                                            <tr>
                                                <th class="text-left">ITEM MODEL</th>
                                                <th class="text-left">ITEM NAME</th>
                                                <th class="text-left">FREE OF CHARGE</th>
                                                <th class="text-right">PRICE</th>
                                                <th class="text-right">QUANTITY</th>
                                                <th class="text-right">AMOUNT</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(terminalItems, index) in terminal.items" :key="'terminal-items-' + index">
                                                <td>{{ terminalItems.model }}</td>
                                                <td>{{ terminalItems.name }}</td>
                                                <td><v-checkbox v-model="terminalItems.free_of_charge" disabled></v-checkbox></td>
                                                <td class="text-right" style="font-family: monospace;">{{ terminalItems.store_price }}</td>
                                                <td class="text-right" style="font-family: monospace;"><input type="number" min="1" v-model="terminalItems.quantity" style="outline: none;" class="text-right" disabled /></td>
                                                <td class="text-right" style="font-family: monospace;">{{ terminalItems.amount }}</td>
                                            </tr>
                                            </tbody>
                                        </template>
                                    </v-simple-table>
                                </div>
                            </v-row>
                        </div>
                        <div style="border-top: 1px solid silver;border-right: 1px solid silver; height:7vh; width:100%;">
                            <v-row no-gutters style="height:100%;width:100%;">
                                <v-col cols="12" sm="3" style="border-right: 1px solid silver;">
                                    <div>QUANTITY</div>
                                    <div><input @keypress="pressQuantity($event)" @blur="blurQuantity()" type="number" min="1" v-model="terminal.settings.quantity" style="outline: none; width: 100%; font-family: monospace;" class="text-right text-lg font-bold" /></div>
                                </v-col>
                                <v-col cols="12" sm="9">
                                    <div>SCAN ITEM MODEL</div>
                                    <div><input v-focus="terminal.settings.focus" @focus="terminal.settings.focus = true" @blur="terminal.settings.focus = false" type="text" v-model="terminal.settings.scan" style="outline: none; width: 100%; font-family: monospace;" class="text-right text-lg font-bold" /></div>
                                </v-col>
                            </v-row>
                        </div>
                    </v-col>
                    <v-col cols="12" sm="4">
                        <div style="height:23vh; width:100%;">
                            <v-row no-gutters style="height:16vh;width:100%;">
                                <v-col style="border-left: 1px solid silver;">
                                    <div>LOGO</div>
                                </v-col>
                            </v-row>
                            <v-row no-gutters style="height:7vh;width:100%;">
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div></div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div></div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div></div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div></div>
                                </v-col>
                            </v-row>
                        </div>
                        <div style="height:28vh; width:100%;">
                            <v-row no-gutters style="height:7vh;">
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>7</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>8</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>9</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>SLOT</div>
                                </v-col>
                            </v-row>
                            <v-row no-gutters style="height:7vh;">
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>4</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>5</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>6</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>SLOT</div>
                                </v-col>
                            </v-row>
                            <v-row no-gutters style="height:7vh;">
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>1</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>2</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>3</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>SLOT</div>
                                </v-col>
                            </v-row>
                            <v-row no-gutters style="height:7vh;">
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>0</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>/</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>C</div>
                                </v-col>
                                <v-col style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>SLOT</div>
                                </v-col>
                            </v-row>
                        </div>
                        <div style="height:36vh; width:100%;">
                            <v-row no-gutters style="height:7vh;">
                                <v-col v-for="n in 4" :key="n" cols="12" sm="3" style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>SLOT</div>
                                </v-col>
                            </v-row>
                            <v-row no-gutters style="height:7vh;">
                                <v-col v-for="n in 4" :key="n" cols="12" sm="3" style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>SLOT</div>
                                </v-col>
                            </v-row>
                            <v-row no-gutters style="height:7vh;">
                                <v-col v-for="n in 4" :key="n" cols="12" sm="3" style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>SLOT</div>
                                </v-col>
                            </v-row>
                            <v-row no-gutters style="height:7vh;">
                                <v-col v-for="n in 4" :key="n" cols="12" sm="3" style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>SLOT</div>
                                </v-col>
                            </v-row>
                            <v-row no-gutters style="height:7vh;">
                                <v-col v-for="n in 4" :key="n" cols="12" sm="3" style="border-left: 1px solid silver;border-top: 1px solid silver;">
                                    <div>SLOT</div>
                                </v-col>
                            </v-row>
                        </div>
                    </v-col>
                </v-row>

            </div>

            <div style="height: 7vh; width:100%; border-bottom: 1px solid silver;">
                <v-row no-gutters style="height:100%;">
                    <v-col @click="selectCustomer('Search Customer', true)" cols="12" sm="1" style="border-right: 1px solid silver; cursor: pointer;">
                        <div>CUSTOMERS</div>
                    </v-col>

                    <v-col @click="selectItem('Search Item', true)" cols="12" sm="1" style="border-right: 1px solid silver; cursor: pointer;">
                        <div>SEARCH ITEM</div>
                    </v-col>

                    <v-col @click="terminal.items = [];terminal.settings.focus = true;" cols="12" sm="1" style="border-right: 1px solid #cbbc70; cursor: pointer;">
                        <div>CLEAR ITEMS</div>
                    </v-col>

                    <v-col v-for="n in 5" :key="'col-1-' + n" cols="12" sm="1" style="border-right: 1px solid silver;">
                        <div>SLOT</div>
                    </v-col>

                    <v-col cols="12" sm="1" style="border-left: 1px solid silver; border-right: 1px solid silver;">
                        <div>SLOT</div>
                    </v-col>

                    <v-col v-for="n in 3" :key="'col-2-' + n" cols="12" sm="1" style="border-right: 1px solid silver;">
                        <div>SLOT</div>
                    </v-col>
                </v-row>
            </div>

            <div style="height: 7vh; width:100%;">
                <v-row no-gutters style="height:100%;">
                    <v-col cols="12" sm="1" style="border-right: 1px solid silver;">
                        <div>SLOT</div>
                    </v-col>

                    <v-col cols="12" sm="1" style="border-right: 1px solid silver;">
                        <div>SLOT</div>
                    </v-col>

                    <v-col cols="12" sm="1" style="border-right: 1px solid silver;">
                        <div>SLOT</div>
                    </v-col>

                    <v-col cols="12" sm="1" style="border-right: 1px solid silver;">
                        <div>SLOT</div>
                    </v-col>
                </v-row>
            </div>

        </div>
    </v-app>
</template>

<script>
export default {
    name: "SalesTerminalComponent",

    mounted() {
        let that = this;

        //this.getItemSeletion();
        //this.getCustomerSelection();

        setTimeout(() => {
            that.terminal.settings.focus = true;
        }, 200);
    },

    directives: {
        focus (el, { value }, { context }) {
            if (value) {

                el.setSelectionRange(0, el.value.trim().length);

                el.focus();
            }
        }
    },

    data() {
        return {
            itemSelection: {
                settings : {
                    title : 'Select',
                    selection : {
                        singleSelect : true,
                    }
                },
                datatable : {
                    search: '',
                    headers: [
                        {
                            text: 'ITEM MODEL',
                            align: 'start',
                            sortable: false,
                            value: 'model',
                        },
                        { text: 'NAME', value: 'name' },
                        { text: 'DESCRIPTION', value: 'description' },
                        { text: 'STORE PRICE', value: 'store_price' },
                        { text: 'QUANTITY', value: 'quantity' }
                    ],
                },
                dialog: false,
                data : [],
                selected : []
            },
            customerSelection:{
                settings : {
                    title : 'Select',
                    selection : {
                        singleSelect : true,
                    }
                },
                datatable : {
                    search: '',
                    headers: [
                        {
                            text: 'NAME',
                            align: 'start',
                            sortable: false,
                            value: 'name',
                        },
                        { text: 'TYPE', value: 'type_description' },
                        { text: 'EMAIL', value: 'email' },
                        { text: 'ADDRESS', value: 'address' }
                    ],
                },
                data: [],
                selected: []
            },
            terminal: {
                settings: {
                    focus: false,
                    quantity: 1,
                    scan: 'SCAN ITEM MODEL'
                },
                message: {
                    dialog: false,
                    value: ''
                },
                items: []
            }
        }
    },

    updated(){

    },

    methods : {
        getItemSeletion() {
            let that = this;

            axios.get('items/getSelection').then((response)=>{
                that.itemSelection.data = response.data.selection;
            })
        },

        getCustomerSelection() {
            let that = this;

            axios.get('customers/getSelection').then((response)=>{
                that.customerSelection.data = response.data.selection;
            })
        },

        selectItem(title, singleSelect, type = null, freeOfCharge = false) {
            this.itemSelection.settings.title = title;
            this.itemSelection.settings.selection.singleSelect = singleSelect;
            this.itemSelection.dialog = true;

            this.itemSelection.selected = [];
        },

        selectCustomer(title, singleSelect, type = null, freeOfCharge = false) {
            this.customerSelection.settings.title = title;
            this.customerSelection.settings.selection.singleSelect = singleSelect;
            this.customerSelection.dialog = true;

            this.customerSelection.selected = [];
        },

        addItemSelection(){
            let that = this;

            let selectedItem = that.itemSelection.selected.reduce(function(result, selectedItem){
                result.push(Object.assign({}, {
                    ...selectedItem,
                    quantity : that.terminal.settings.quantity,
                    amount: parseFloat(selectedItem.store_price.replace(',','')) * that.terminal.settings.quantity
                }));

                return result;
            }, []);

            that.terminal.items = that.terminal.items.concat(selectedItem);
            that.terminal.settings.quantity = 1;
            that.terminalItemsScrollToEnd();

            that.itemSelection.dialog = false;
            that.terminal.settings.focus = true;
        },

        addCustomerSelection(){
            let that = this;

            alert(that.customerSelection.selected[0].name);

            that.customerSelection.dialog = false;
            that.terminal.settings.focus = true;
        },

        scan(model){
            let that = this;

            let scan = _.find(that.itemSelection.data, function(item) { return item.model === model; });

            if (scan) {

                let scannedItem = Object.assign({}, {
                    ...scan,
                    quantity : that.terminal.settings.quantity,
                    amount: parseFloat(scan.store_price.replace(',','')) * that.terminal.settings.quantity
                });

                that.terminal.items = that.terminal.items.concat(scannedItem);
                that.terminal.settings.quantity = 1;
                that.terminalItemsScrollToEnd();
            } else {
                that.terminal.message.value = "ITEM MODEL NOT FOUND.";
                that.terminal.message.dialog = true;
                that.terminal.settings.focus = false;
            }
        },

        terminalItemsScrollToEnd(){
            let that = this;

            let terminalItems = that.$el.querySelector("#terminalItems");
            let overflow = that.$el.querySelector("#terminalItemsOverflow");

            let scroll = terminalItems.clientHeight * terminalItems.clientHeight;

            setTimeout(()=>{
                overflow.scrollTop = scroll;
            }, 100);
        },

        pressQuantity(event) {
            let charCode = (event.which) ? event.which : event.keyCode;

            if(charCode < 48 || charCode > 57){
                event.preventDefault();
            } else {
                return true;
            }
        },

        blurQuantity(){
            let that = this;

            let quantity = that.terminal.settings.quantity;

            that.terminal.settings.quantity = (!isNaN(quantity) && quantity && (Math.abs(parseInt(quantity)) > 0)) ? Math.abs(parseInt(quantity)) : 1;
        }
    },

    watch: {
        'terminal.settings.scan' : {
            handler(value){
                let that = this;

                if(value !== 'SCAN ITEM MODEL'){
                    that.scan(value);
                }

                that.terminal.settings.scan = 'SCAN ITEM MODEL';
            }
        },
        'terminal.settings.quantity' : {
            handler(){
                let that = this;

                that.terminal.settings.focus = true;
            }
        },
    }
}
</script>

<style scoped>

</style>
