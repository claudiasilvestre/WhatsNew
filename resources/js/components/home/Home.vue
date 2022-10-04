<template>
  <v-app>
    <div>
      <app-header />
      <audiovisuales />
      <app-footer />
    </div>
  </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import Footer from '../layouts/Footer.vue'
import Audiovisuales from './Audiovisuales.vue'

export default {
    name: 'Home',
    components: {
      'app-header': Header,
      'app-footer': Footer,
      Audiovisuales,
    },
    mounted() {
        document.title = "Home - WhatsNew"
    }
}
</script>
