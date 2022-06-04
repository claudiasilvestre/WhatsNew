const state = {
    user: {},
    errors: {}
};
const getters = {};
const actions = {
    loginUser( {commit}, formData ) {
        axios.get('/sanctum/csrf-cookie').then(response => {
            axios.post('/api/login', formData).then((response) => {
                window.location.replace("/")
            }).catch((errors) => {
                commit('setErrors', errors.response.data.errors)
            })
        });
    },
    getUser( {commit} ) {
        axios.get('/api/user')
            .then(response => {
                commit('setUser', response.data) })
            .catch(error => { console.log(error.response) });
    }
};
const mutations = {
    setUser( state, data ) {
        state.user = data;
    },
    setErrors( state, data ) {
        state.errors = data
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}