<template>
  <v-container fluid>
    <v-sheet
      class="rounded"
      dark
    >
      <v-slide-group
        multiple
        show-arrows
        dark
      >
        <v-slide-item
          class="ma-4"
          v-for="peliculaPopular in audiovisualesPopulares['peliculas']"
          :key="peliculaPopular.id"
        >
          <v-btn
            class="mx-3"
            :width="185"
            :height="270"
          >
            <v-img
              class="rounded"
              :src="peliculaPopular.cartel"
              :alt="peliculaPopular.titulo"
              :width="185"
              :height="270"
            >
            </v-img>
          </v-btn>
        </v-slide-item>
      </v-slide-group>
    </v-sheet>
  </v-container>
</template>

<script>
  export default {
    data() {
        return {
            audiovisualesPopulares: []
        }
    },
    created() {
        axios.get('/audiovisuales')
            .then(response => this.audiovisualesPopulares = response.data)
            .catch(error => { console.log(error.response) });
    }
  }
</script>