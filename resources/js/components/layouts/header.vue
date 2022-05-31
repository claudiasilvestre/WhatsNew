<template>
    <header v-if="usuario">
        <router-link :to="{ name: 'home' }">
            <h3>{{ title }}</h3>
        </router-link>
        <p>{{ usuario.nombre }}</p>
    </header>
</template>

<script>
export default {
    data() {
        return {
            title: "What's New",
            usuario: null
        }
    },
    mounted() {
        axios.get('/api/user')
            .then(response => this.usuario = response.data)
            .catch(error => { console.log(error.response) });
    }
}
</script>

<style>
    @import '/css/styles/styles.css';
</style>
