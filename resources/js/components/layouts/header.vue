<template>
    <header class="header-content">
        <router-link :to="{ name: 'home' }">
            <h3>{{ title }}</h3>
        </router-link>
        <div class="d-flex flex-row search rounded">
            <input type="search" name="search" v-model="busqueda" placeholder="Buscar" required/>
            <button type="submit" @click="redirect()">Search</button>
        </div>
        <div v-if="Object.keys(currentUser).length > 0">
            <router-link :to="{ name: 'perfil', params: { idPersona: currentUser.id }}">
                <span>{{ currentUser.nombre }}</span>
            </router-link>
            <button @click="handleLogout" class="btn btn-danger">Cerrar sesión</button>
        </div>
    </header>
</template>

<script>
export default {
    data() {
        return {
            title: "What's New",
            busqueda: ''
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
            this.$store.dispatch('currentUser/logoutUser');
        },
        redirect() {
            if (this.busqueda)
                this.$router.push('/search/'+this.busqueda)
        }
    }
}
</script>

<style>
    @import '/css/styles/styles.css';
</style>
