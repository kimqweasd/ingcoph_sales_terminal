export default class Api {

    constructor(attributes) {
        this.address = {
            authorization : attributes.address.authorization || null,
            token : attributes.address.token || null,
            library : attributes.address.library || null,
        }

        this.scopes = attributes.scopes;
        this.responses = attributes.responses;
        this.transformers = attributes.transformers;
        this.key = attributes.key;
    }

    get tokenAddress() {
        return this.address.token;
    }

    get libraryAddress() {
        return this.address.library;
    }
}
