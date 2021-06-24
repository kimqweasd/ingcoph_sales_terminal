window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.salesTerminalAxios = axios.create();
window.salesTerminalAxios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.salesTerminalAxios.defaults.headers.common['Content-Type'] = 'application/json';
window.salesTerminalAxios.defaults.headers.common['Accept'] = 'application/json';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

import Vue from "vue";
import Moment from 'moment';
import Vuetify from 'vuetify';
import '@mdi/font/css/materialdesignicons.css';

window.Vue = Vue;
window.Moment = Moment;
window.Vuetify = Vuetify;

window.Vue.use(Vuetify);

export default new Vuetify({
    icons: {
        iconfont: 'mdi'
    },
})