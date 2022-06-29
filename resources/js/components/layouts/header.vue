<template>
    <header class="header-content">
        <router-link :to="{ name: 'home' }">
            <h3>{{ title }}</h3>
        </router-link>
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
        }
    }
}
</script>

<style>
    @import '/css/styles/styles.css';
</style>
