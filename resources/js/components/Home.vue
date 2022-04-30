<template>
  <v-app>
    <div>
        <app-header />
        <prueba />
    </div>
  </v-app>
</template>

<script>
import Header from './layouts/Header.vue'
import PeliculasPopulares from './PeliculasPopulares.vue'
import Prueba from './Prueba.vue'

export default {
    name: 'Home',

    components: {
        'app-header': Header,
        PeliculasPopulares,
        Prueba,
    }
}
</script>
