import Application from "../Abstract/Application";

export default class StoreHub extends Application{

    constructor(attributes) {
        super({...attributes});
    }

    authenticate(args) {

        return window[args.api].post(this.tokenAddress, {
            ...args.form,
            grant_type: 'password',
            client_id: process.env.MIX_CLIENT_ID,
            client_secret: process.env.MIX_CLIENT_SECRET,
            scope: '*'
        });
    }

    refresh(args) {

        return window[args.api].post(this.tokenAddress, {
            ...args.form,
            grant_type: 'refresh_token',
            client_id: process.env.MIX_CLIENT_ID,
            client_secret: process.env.MIX_CLIENT_SECRET,
            scope: '*'
        });
    }

    account() {
        return `${this.libraryAddress}/me`;
    }

    storeInfo() {
        return `${this.libraryAddress}/store`;
    }

    items() {
        return `${this.libraryAddress}/items`;
    }
}
