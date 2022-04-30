require('./bootstrap');

window.Vue = require('vue').default;

import VueRouter from 'vue-router'
import routes from './routes';
import Vuetify from '../plugins/vuetify'

Vue.use(VueRouter)

const app = new Vue({
    vuetify: Vuetify,
    el: '#app',
    router: new VueRouter(routes)
});
