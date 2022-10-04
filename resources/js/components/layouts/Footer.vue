<template>
    <footer>
        <span>&copy; {{ year }} {{ title }}</span>
    </footer>
</template>

<script>
export default {
    data() {
        return {
            title: "WhatsNew",
            year: new Date().getFullYear(),
        }
    },
}
</script>