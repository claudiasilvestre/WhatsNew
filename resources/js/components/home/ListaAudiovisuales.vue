<template>
  <div class="listAudiovisuales">
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
          v-for="audiovisual in audiovisuales"
          :key="audiovisual.id"
        >
          <router-link :to="{ name: 'audiovisual', params: { id: audiovisual.id }}">
            <v-img
              class="rounded"
              :src="audiovisual.cartel"
              :alt="audiovisual.titulo"
              :width="185"
              :height="270"
            >
            </v-img>
          </router-link>
        </v-slide-item>
      </v-slide-group>
    </v-sheet>
  </div>
</template>

<script>
  export default {
    props: {
      audiovisuales: {
        required: true,
        type: Array
      }
    }
  }
</script>
