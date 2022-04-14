<template>
    <div>
        <app-header />
    </div>
</template>

<script>
import header from './layouts/header.vue'

export default {
    name: 'Home',

    components: {
        'app-header': header
    }
}
</script>
