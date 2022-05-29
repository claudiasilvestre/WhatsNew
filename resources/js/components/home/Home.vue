<template>
  <v-app>
    <div>
      <app-header />
      <audiovisuales-populares />
    </div>
  </v-app>
</template>

<script>
import Registro from '../auth/Registro.vue'
import Login from '../auth/Login.vue'
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
