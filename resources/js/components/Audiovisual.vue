<template>
    <div>
        <div><h1> Menú </h1></div>
        <div><h1> {{ $route.params.audiovisual.titulo }} </h1></div>
        <info 
            :audiovisual="$route.params.audiovisual"
        />
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