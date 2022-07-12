<template>
    <header class="header-content">
        <div class="header-content">
            <router-link :to="{ name: 'home' }">
                <h3 class="pr-3">{{ title }}</h3>
            </router-link>
            <router-link :to="{ name: 'coleccion' }">
                <span class="pl-3">Mi colección</span>
            </router-link>
        </div>
        <div class="d-flex flex-row search rounded">
            <input type="search" @keydown.enter="redirectBusqueda()" name="search" v-model="busqueda" placeholder="Buscar" required/>
            <button type="submit" @click="redirectBusqueda()">Search</button>
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
        redirectPerfil() {
            this.$router.push('/perfil/'+this.currentUser.id)
        },
        redirectBusqueda() {
            if (this.busqueda)
                this.$router.push('/search/'+this.busqueda)
        }
    }
}
</script>

<style>
    @import '/css/styles/styles.css';
</style>
