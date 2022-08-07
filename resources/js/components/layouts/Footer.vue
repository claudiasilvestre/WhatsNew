<template>
    <footer>
        <span>&copy; {{ year }} {{ title }}</span>
    </footer>
</template>

<script>
export default {
    data() {
        return {
            title: "What's New",
            year: new Date().getFullYear(),
        }
    },
}
</script>