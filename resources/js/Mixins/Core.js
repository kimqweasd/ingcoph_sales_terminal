import api from '../api';
import AnimatedNumber from "animated-number-vue";

export default {
    components: {
        AnimatedNumber
    },

    data() {
        return {
            log: 0,
            apiInterface: api
        }
    },

    methods: {
        refreshToken(args, app, callback = null){
            let that = this;

            that.apiInterface[app].refresh({
                api: app,
                form: args
            }).then((response) => {
                that.messages.push(`[${that.log++}] Refreshing access token...`);

                auth.api = app;
                auth.access_token = response.data.access_token;
                auth.refresh_token = response.data.refresh_token;

                window[app].defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;

                that.sessionTokenRefresh({...response.data, api: app}, callback);
            }).catch((error) => {
                if (error.response) {
                    that.errors = ['Access token refresh failed'];
                } else if (error.request) {
                    that.errors = ['Failed to connect to store hub server...'];
                } else {
                    that.errors = ['Something went wrong... Please contact your administrator...'];
                    console.log('Something went wrong...', error.message);
                }
            });
        },

        sessionTokenRefresh(data, callback = null) {
            let that = this;

            window.salesTerminalAxios.post('login', {
                api: data.api,
                access_token: data.access_token,
                refresh_token: data.refresh_token
            }).then((response) => {
                that.messages.push(`[${that.log++}] Access token refreshed...`);
                if (!_.isNull(callback)) {
                    callback();
                }
            }).catch((error) => {
                that.errors = ['Access token refresh failed'];
                console.log(error.response.data);
            });
        }
    }
}
