const state = {
    user: {},
    errors: {},
};
const mutations = {
    setUser( state, data ) {
        state.user = data;
    },
    setErrors( state, data ) {
        state.errors = data
    },
};
const actions = {
    loginUser( {commit, state}, formData ) {
        axios.get('/sanctum/csrf-cookie').then(response => {
            axios.post('/api/login', formData).then((response) => {
                window.location.replace("/home");
            }).catch((errors) => {
                commit('setErrors', errors.response.data.errors)
            })
        });
    },
    getUser( {commit, state} ) {
        return axios.get('/api/user')
            .then(response => {
                commit('setUser', response.data); })
            .catch(error => { console.log(error.response) });
    },
    logoutUser( {commit, state} ) {
        axios.post('/api/logout')
            .then(response => {
                window.location.replace("/");
                commit('setUser', {});})
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