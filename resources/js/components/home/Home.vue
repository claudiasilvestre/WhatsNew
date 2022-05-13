<template>
  <v-app>
    <div>
        <app-header />
        <audiovisuales-populares />
    </div>
  </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import AudiovisualesPopulares from './AudiovisualesPopulares.vue'

export default {
    name: 'Home',

    components: {
        'app-header': Header,
        AudiovisualesPopulares,
    }
}
</script>
