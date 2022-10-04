<template>
    <header>
        <h3 class="pr-3">{{ title }}</h3>
    </header>
</template>

<script>
export default {
    data() {
        return {
            title: "WhatsNew",
        }
    },
    mounted() {
        document.title = "WhatsNew"
    }
}
</script>
