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
          class="ma-6"
          v-for="audiovisualPopular in audiovisualesPopulares"
          :key="audiovisualPopular.id"
        >
          <router-link :to="{ name: 'audiovisual', params: { id: audiovisualPopular.id, audiovisual: audiovisualPopular }}">
            <v-img
              class="rounded"
              :src="audiovisualPopular.cartel"
              :alt="audiovisualPopular.titulo"
              :width="185"
              :height="270"
            >
            </v-img>
          </router-link>
        </v-slide-item>
      </v-slide-group>
    </v-sheet>
  </v-container>
</template>

<script>
  export default {
    props: {
      audiovisualesPopulares: {
          required: true
      }
    }
  }
</script>