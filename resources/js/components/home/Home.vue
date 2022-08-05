<template>
  <v-app>
    <div>
      <app-header />
      <audiovisuales />
    </div>
  </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import Audiovisuales from './Audiovisuales.vue'

export default {
    name: 'Home',
    components: {
      'app-header': Header,
      Audiovisuales,
    },
    mounted() {
        document.title = "Home - What's new"
    }
}
</script>
