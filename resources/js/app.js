require('./bootstrap');

Object.defineProperty(window.Vue.prototype, 'Lodash', {value: window._});
Object.defineProperty(window.Vue.prototype, 'WindowLocation', {value: window.location});
Object.defineProperty(window.Vue.prototype, 'Moment', {value: Moment});

window.Vue.component('sales-terminal-component', require('./components/SalesTerminalComponent.vue').default);
window.Vue.component('login-component', require('./components/Auth/Login.vue').default);
window.Vue.component('utility-component', require('./components/Utility').default);

new window.Vue({
    el: '#app',
    vuetify: new Vuetify(),
});
