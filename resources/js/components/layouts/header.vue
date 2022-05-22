<template>
    <header>
        <router-link :to="{ name: 'home' }">
            <h3>{{ title }}</h3>
        </router-link>
    </header>
</template>

<script>
export default {
    data() {
        return {
            title: "What's New"
        }
    }
}
</script>

<style>
    @import '/css/styles/styles.css';
</style>
