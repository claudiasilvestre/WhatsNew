const state = {
    usuario: {},
    errors: {},
};

const mutations = {
    setUsuario( state, data ) {
        state.usuario = data;
    },
    setErrors( state, data ) {
        state.errors = data
    },
};

const actions = {
    inicioSesionUsuario( {commit, state}, formData ) {
        axios.get('/sanctum/csrf-cookie').then(response => {
            axios.post('/api/inicio-sesion', formData).then((response) => {
                window.location.replace("/home");
            }).catch((errors) => {
                commit('setErrors', errors.response.data)
            })
        });
    },
    obtenerUsuario( {commit, state} ) {
        return axios.get('/api/usuario')
            .then(response => {
                commit('setUsuario', response.data); })
            .catch(error => { console.log(error.response) });
    },
    cierreSesionUsuario( {commit, state} ) {
        axios.post('/api/cierre-sesion')
            .then(response => {
                window.location.replace("/");
                commit('setUsuario', {});})
            .catch(error => console.log(error))
    }
};

const getters = {};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}