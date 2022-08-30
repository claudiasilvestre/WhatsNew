import Vue from "vue"
import Vuex from "vuex"

import usuarioActual from "./modules/usuarioActual"

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        usuarioActual
    }
})
