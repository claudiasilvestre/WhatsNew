require('./bootstrap');

window.Vue = require('vue').default;

import VueRouter from 'vue-router'
import routes from './routes';
import Vuetify from '../plugins/vuetify'
import store from './store'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(VueRouter)

const app = new Vue({
    vuetify: Vuetify,
    store,
    el: '#app',
    router: routes
});
