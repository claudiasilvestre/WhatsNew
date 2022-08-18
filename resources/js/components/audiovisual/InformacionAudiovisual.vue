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
          <div class="width-info mr-4">
            <h5>Título original</h5>
            <p>{{ audiovisual.tituloOriginal }}</p>
            <h5>Estreno</h5>
            <p>{{ audiovisual.fechaLanzamiento }}</p>
            <h5>Sinopsis</h5>
            <p>{{ audiovisual.sinopsis }}</p>
          </div>
          <div class="width-platforms">
            <h5 v-if="stream.length > 0 || alquilar.length > 0 || comprar.length > 0">Ver ahora</h5>
            <div v-if="stream.length > 0" class="mt-2 mb-2">
              <span>Stream</span>
              <div class="d-flex flex-wrap">
                <div v-for="proveedor in stream" :key="proveedor.id" class="mr-2 mb-2">
                  <img class="rounded" v-bind:src="proveedor.logo" v-bind:alt="proveedor.nombre" width="45" height="45">
                </div>
              </div>
            </div>
            <div v-if="alquilar.length > 0" class="mt-2 mb-2">
              <span>Alquilar</span>
              <div class="d-flex flex-wrap">
                <div v-for="proveedor in alquilar" :key="proveedor.id" class="mr-2 mb-2">
                  <img class="rounded" v-bind:src="proveedor.logo" v-bind:alt="proveedor.nombre" width="45" height="45">
                </div>
              </div>
            </div>
            <div v-if="comprar.length > 0" class="mt-2 mb-2">
              <span>Comprar</span>
              <div class="d-flex flex-wrap">
                <div v-for="proveedor in comprar" :key="proveedor.id" class="mr-2 mb-2">
                  <img class="rounded" v-bind:src="proveedor.logo" v-bind:alt="proveedor.nombre" width="45" height="45">
                </div>
              </div>
            </div>
          </div>
        </div>

        <h5 v-if="personas_reparto.length > 0">Reparto</h5>
        <div v-if="!readMoreActivatedReparto && personas_reparto.length > 5" class="d-flex flex-column mb-4">
          <div class="d-flex flex-wrap">
            <div v-for="i in 5" :key="i" class="mr-2">
              <router-link :to="{ name: 'informacion', params: { idPersona: personas_reparto[i-1].persona_id }}">
                <img v-if="personas_reparto[i-1].foto" class="rounded" v-bind:src="personas_reparto[i-1].foto" v-bind:alt="personas_reparto[i-1].nombre" width="115" height="170">
                <img v-else class="rounded" src="/img/blank-profile-picture.jpg" v-bind:alt="personas_reparto[i-1].nombre" width="115" height="170">
              </router-link>
              <p class="d-flex flex-column">
                <router-link :to="{ name: 'informacion', params: { idPersona: personas_reparto[i-1].persona_id }}">
                  <span>{{ personas_reparto[i-1].nombre }}</span>
                </router-link>
                <span class="p-letra">{{ personas_reparto[i-1].personaje }}</span>
              </p>
            </div>
          </div>
          <a @click="activateReadMoreReparto">Mostrar más</a>
        </div>
        <div v-else class="d-flex flex-wrap mb-4">
          <div v-for="participacion in personas_reparto" :key="participacion.id" class="mr-2">
            <router-link :to="{ name: 'informacion', params: { idPersona: participacion.persona_id }}">
              <img v-if="participacion.foto" class="rounded" v-bind:src="participacion.foto" v-bind:alt="participacion.nombre" width="115" height="170">
              <img v-else class="rounded" src="/img/blank-profile-picture.jpg" v-bind:alt="participacion.nombre" width="115" height="170">
            </router-link>
            <p class="d-flex flex-column">
              <router-link :to="{ name: 'informacion', params: { idPersona: participacion.persona_id }}">
                <span>{{ participacion.nombre }}</span>
              </router-link>
              <span class="p-letra">{{ participacion.personaje }}</span>
            </p>
          </div>
        </div>

        <h5 v-if="personas_equipo.length > 0">Equipo</h5>
        <div v-if="!readMoreActivatedEquipo && personas_equipo.length > 5" class="d-flex flex-column mb-4">
          <div class="d-flex flex-wrap">
            <div v-for="i in 5" :key="i" class="mr-2">
              <router-link :to="{ name: 'informacion', params: { idPersona: personas_equipo[i-1].persona_id }}">
                <img v-if="personas_equipo[i-1].foto" class="rounded" v-bind:src="personas_equipo[i-1].foto" v-bind:alt="personas_equipo[i-1].nombre" width="115" height="170">
                <img v-else class="rounded" src="/img/blank-profile-picture.jpg" v-bind:alt="personas_equipo[i-1].nombre" width="115" height="170">
              </router-link>
              <p class="d-flex flex-column">
                <router-link :to="{ name: 'informacion', params: { idPersona: personas_equipo[i-1].persona_id }}">
                  <span>{{ personas_equipo[i-1].nombre }}</span>
                </router-link>
                <span v-if="personas_equipo[i-1].tipoParticipante_id === 2" class="p-letra">Director</span>
                <span v-else class="p-letra">Guionista</span>
              </p>
            </div>
          </div>
          <a @click="activateReadMore">Mostrar más</a>
        </div>
        <div v-else class="d-flex flex-wrap mb-4">
          <div v-for="participacion in personas_equipo" :key="participacion.id" class="mr-2">
            <div v-if="participacion.tipoParticipante_id !== 1">
              <router-link :to="{ name: 'informacion', params: { idPersona: participacion.persona_id }}">
                <img v-if="participacion.foto" class="rounded" v-bind:src="participacion.foto" v-bind:alt="participacion.nombre" width="115" height="170">
                <img v-else class="rounded" src="/img/blank-profile-picture.jpg" v-bind:alt="participacion.nombre" width="115" height="170">
              </router-link>
              <p class="d-flex flex-column">
                <router-link :to="{ name: 'informacion', params: { idPersona: participacion.persona_id }}">
                  <span>{{ participacion.nombre }}</span>
                </router-link>
                <span v-if="participacion.tipoParticipante_id === 2" class="p-letra">Director</span>
                <span v-else class="p-letra">Guionista</span>
              </p>
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
            personas_reparto: [],
            personas_equipo: [],
            stream: [],
            alquilar: [],
            comprar: [],
            loading: true,
            readMoreActivatedReparto: false,
            readMoreActivatedEquipo: false,
        }
    },
    created() {
        axios.get('/api/personas-participacion/'+this.audiovisual.id)
            .then(response => {
              this.personas_reparto = response.data['personas_reparto']
              this.personas_equipo = response.data['personas_equipo']
            })
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
    methods: {    
      activateReadMoreReparto() {
        this.readMoreActivatedReparto = true;
      },
      activateReadMoreEquipo() {
        this.readMoreActivatedEquipo = true;
      },
    }
}
</script>