<template>
    <div>
        <app-header />
        <peliculas-populares />
    </div>
</template>

<script>
import Header from './layouts/Header.vue'
import PeliculasPopulares from './PeliculasPopulares.vue'

export default {
    name: 'Home',

    components: {
        'app-header': Header,
        PeliculasPopulares,
    }
}
</script>
