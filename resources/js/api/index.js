import StoreHub from "../Classes/Concrete/StoreHub";

export default {
    storeHub : new StoreHub({
        name : 'storeHub',
        namePresenter: 'Store Hub',
        address : {
            token : 'http://127.0.0.1:9696/oauth/token',
            library : 'http://127.0.0.1:9696/api/v1',
        },
    })
}
