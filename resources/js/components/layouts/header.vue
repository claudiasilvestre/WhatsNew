<template>
    <header>
        <h3>{{ title }}</h3>
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

<style scoped>
    header {
        background-color: black;
        padding: 10px;
        color: white;
        margin: auto;
    }
</style>
