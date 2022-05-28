<template>
    <div class="list">
        <h5>Título original</h5>
        <p>{{ audiovisual.tituloOriginal }}</p>
        <h5>Estreno</h5>
        <p>{{ audiovisual.fechaLanzamiento }}</p>
        <h5>Sinopsis</h5>
        <p>{{ audiovisual.sinopsis }}</p>

        <h5>Reparto</h5>
        <div class="d-flex flex-wrap">
          <div v-for="participacion in participaciones" :key="participacion.id" class="mr-2">
            <div v-if="participacion.tipoParticipante_id === 1">
              <router-link :to="{ name: 'informacion', params: { idAudiovisual: audiovisual.id, idPersona: participacion.persona_id }}">
                <img class="rounded" v-bind:src="participacion.foto" v-bind:alt="participacion.nombre" width="115" height="170">
              </router-link>
              <router-link :to="{ name: 'informacion', params: { idAudiovisual: audiovisual.id, idPersona: participacion.persona_id }}">
                <p>{{ participacion.nombre }}</p>
              </router-link>
              <p>{{ participacion.personaje }}</p>
            </div>
          </div>
        </div>

        <h5>Equipo</h5>
        <div class="d-flex flex-wrap">
          <div v-for="participacion in participaciones" :key="participacion.id" class="mr-2">
            <div v-if="participacion.tipoParticipante_id !== 1">
              <router-link :to="{ name: 'informacion', params: { idAudiovisual: audiovisual.id, idPersona: participacion.persona_id }}">
                <img class="rounded" v-bind:src="participacion.foto" v-bind:alt="participacion.nombre" width="115" height="170">
              </router-link>
              <router-link :to="{ name: 'informacion', params: { idAudiovisual: audiovisual.id, idPersona: participacion.persona_id }}">
                <p>{{ participacion.nombre }}</p>
              </router-link>
              <p v-if="participacion.tipoParticipante_id === 2">Director</p>
              <p v-else>Guionista</p>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
      audiovisual: {
        required: true,
        type: Object
      }
    },
    data() {
        return {
            participaciones: [],
        }
    },
    created() {
        axios.get('/api/personas-participacion/'+this.audiovisual.id)
            .then(response => this.participaciones = response.data)
            .catch(error => { console.log(error.response) });
    },
}
</script>