<template>
    <div>
        <div><h1> Menú </h1></div>
        <div><h1> {{ $route.params.id }} </h1></div>
    </div>
</template>

<script>
import Info from './Info.vue'

export default {
    components: {
        Info
    }
}
</script>