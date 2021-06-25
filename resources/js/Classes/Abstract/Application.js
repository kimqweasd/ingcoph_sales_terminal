import Api from "./Api";

export default class Application extends Api{

    constructor(attributes) {
        super({
            address : attributes.address || {},
            scopes : attributes.scopes || [],
            responses : attributes.responses || {},
            transformers : attributes.transformers || {},
            key : attributes.key || null,
        });

        this.name = attributes.name || "application";
        this.state = attributes.state || null;
        this.images = attributes.images || {
            icon : {},
            logo : {}
        };
    }

    get appName() {
        return this.name;
    }
}
