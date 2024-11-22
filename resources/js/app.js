/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

require('./bootstrap');

window.Vue = require('vue');

Vue.component('example-component', require('./components/ExampleComponent').default);
Vue.component('login-component', require('./components/gyo/LoginComponent').default);
Vue.component('dashboard-component', require('./components/gyo/DashboardComponent').default);

const app = new Vue({
    el: '#app'
});