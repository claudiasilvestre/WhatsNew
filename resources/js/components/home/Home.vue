<template>
  <v-app>
    <div>
      <registro />
      <!-- <login />
      <app-header />
      <audiovisuales-populares /> -->
    </div>
  </v-app>
</template>

<script>
import Registro from '../Registro.vue'
import Login from '../Login.vue'
import Header from '../layouts/Header.vue'
import AudiovisualesPopulares from './AudiovisualesPopulares.vue'

export default {
    name: 'Home',

    components: {
        Registro,
        Login,
        'app-header': Header,
        AudiovisualesPopulares,
    }
}
</script>
