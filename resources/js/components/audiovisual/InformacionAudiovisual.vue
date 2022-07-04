<template>
    <div class="list">
      <div v-if="loading" class="d-flex justify-content-center flex-column align-items-center" style="height:40vh;">
        <b-spinner
            :variant="'light'"
            :key="'light'"
        ></b-spinner>
      </div>
      <div v-else>
        <div class="row-between">
          <div>
            <h5>Título original</h5>
            <p>{{ audiovisual.tituloOriginal }}</p>
            <h5>Estreno</h5>
            <p>{{ audiovisual.fechaLanzamiento }}</p>
            <h5>Sinopsis</h5>
            <p>{{ audiovisual.sinopsis }}</p>
          </div>
          <div>
            <h5 v-if="stream.length > 0 || alquilar.length > 0 || comprar.length > 0">Ver ahora</h5>
              <div v-if="stream.length > 0" class="m-2">
                <span>Stream</span>
                <div class="d-flex flex-row">
                  <div v-for="proveedor in stream" :key="proveedor.id" class="mr-2">
                    <img class="rounded" v-bind:src="proveedor.logo" v-bind:alt="proveedor.nombre" width="45" height="45">
                  </div>
                </div>
              </div>
              <div v-if="alquilar.length > 0" class="m-2">
                <span>Alquilar</span>
                <div class="d-flex flex-row">
                  <div v-for="proveedor in alquilar" :key="proveedor.id" class="mr-2">
                    <img class="rounded" v-bind:src="proveedor.logo" v-bind:alt="proveedor.nombre" width="45" height="45">
                  </div>
                </div>
              </div>
              <div v-if="comprar.length > 0" class="m-2">
                <span>Comprar</span>
                <div class="d-flex flex-row">
                  <div v-for="proveedor in comprar" :key="proveedor.id" class="mr-2">
                    <img class="rounded" v-bind:src="proveedor.logo" v-bind:alt="proveedor.nombre" width="45" height="45">
                  </div>
                </div>
              </div>
          </div>
        </div>

        <h5>Reparto</h5>
        <div class="d-flex flex-wrap">
          <div v-for="participacion in participaciones" :key="participacion.id" class="mr-2">
            <div v-if="participacion.tipoParticipante_id === 1">
              <router-link :to="{ name: 'informacion', params: { idPersona: participacion.persona_id }}">
                <img v-if="participacion.foto" class="rounded" v-bind:src="participacion.foto" v-bind:alt="participacion.nombre" width="115" height="170">
                <img v-else class="rounded" src="/img/blank-profile-picture.jpg" v-bind:alt="participacion.nombre" width="115" height="170">
              </router-link>
              <router-link :to="{ name: 'informacion', params: { idPersona: participacion.persona_id }}">
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
              <router-link :to="{ name: 'informacion', params: { idPersona: participacion.persona_id }}">
                <img v-if="participacion.foto" class="rounded" v-bind:src="participacion.foto" v-bind:alt="participacion.nombre" width="115" height="170">
                <img v-else class="rounded" src="/img/blank-profile-picture.jpg" v-bind:alt="participacion.nombre" width="115" height="170">
              </router-link>
              <router-link :to="{ name: 'informacion', params: { idPersona: participacion.persona_id }}">
                <p>{{ participacion.nombre }}</p>
              </router-link>
              <p v-if="participacion.tipoParticipante_id === 2">Director</p>
              <p v-else>Guionista</p>
            </div>
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
            stream: [],
            alquilar: [],
            comprar: [],
            loading: true,
        }
    },
    created() {
        axios.get('/api/personas-participacion/'+this.audiovisual.id)
            .then(response => this.participaciones = response.data)
            .catch(error => { console.log(error.response) });

        axios.get('/api/proveedores/'+this.audiovisual.id)
            .then(response => { 
              this.stream = response.data['stream'];
              this.alquilar = response.data['alquilar'];
              this.comprar = response.data['comprar'];
            })
            .catch(error => { console.log(error.response) })
            .finally(() => this.loading = false);
    },
}
</script>