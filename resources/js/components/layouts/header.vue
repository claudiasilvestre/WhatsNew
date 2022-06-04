<template>
    <header class="header-content">
        <router-link :to="{ name: 'home' }">
            <h3>{{ title }}</h3>
        </router-link>
        <div>
            <span>{{ currentUser.nombre }}</span>
            <button @click="handleLogout" class="btn btn-danger">Cerrar sesión</button>
        </div>
    </header>
</template>

<script>
export default {
    data() {
        return {
            title: "What's New",
        }
    },
    computed: {
        currentUser: {
            get() {
                return this.$store.state.currentUser.user;
            }
        }
    },
    created() {
        this.$store.dispatch('currentUser/getUser');
    },
    methods: {
        handleLogout() {
            axios.post('/api/logout')
            .then(response => {
                this.$router.push('/login')
                console.log(response.data)})
            .catch(error => console.log(error.response))
        }
    }
}
</script>

<style>
    @import '/css/styles/styles.css';
</style>
