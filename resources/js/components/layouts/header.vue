<template>
    <header class="header-content">
        <div class="header-content">
            <router-link :to="{ name: 'home' }">
                <h3 class="pr-3">{{ title }}</h3>
            </router-link>
            <router-link :to="{ name: 'coleccion' }">
                <span class="p-3">Mi colección</span>
            </router-link>
        </div>
        <div class="d-flex flex-row search rounded">
            <input type="search" @keydown.enter="redirectBusqueda()" v-model="busqueda" placeholder="Buscar" required/>
            <button type="submit" @click="redirectBusqueda()">Buscar</button>
        </div>
        <div v-if="Object.keys(currentUser).length > 0" class="header-content">
            <router-link :to="{ name: 'perfil', params: { idPersona: currentUser.id }}">
                <span class="p-2">{{ currentUser.nombre }}</span>
            </router-link>
            <b-icon icon="box-arrow-left" class="h3 pointer m-2" @click="handleLogout"></b-icon>
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
    methods: {
        handleLogout() {
            this.$store.dispatch('currentUser/logoutUser');
        },
        redirectBusqueda() {
            if (this.busqueda)
                this.$router.push('/busqueda/'+this.busqueda)
        }
    }
}
</script>

<style>
    @import '/css/styles/styles.css';
</style>
