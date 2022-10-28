<template>
    <div>
        <div class="d-flex flex-row align-items-center mb-1">
            <span class="ml-2">Puntúa</span>
            <star-rating v-model="rating" :increment="0.5" :star-size="25" :show-rating="false" text-class="custom-text" class="m-2"></star-rating>
            <b-icon v-if="rating !== 0" icon="x-circle" variant="danger" @click="borrarValoracion()" class="pointer"></b-icon>
        </div>
        <div>
            <button type="button" v-bind:class="{'color-a': clicked1}" @click="seguimiento(1)" class="p-2 background2 rounded"><b-icon icon="clock" class="icon-style"></b-icon>
                {{ pendiente }}
            </button>
            <button type="button" v-if="audiovisual.tipoAudiovisual_id === 2" v-bind:class="{'color-a': clicked2}" @click="seguimiento(2)" class="p-2 background2 rounded"><b-icon icon="eye" class="icon-style"></b-icon>
                {{ seguir }}
            </button>
            <button type="button" v-bind:class="{'color-a': clicked3}" @click="seguimiento(3)" class="p-2 background2 rounded"><b-icon icon="check-circle" class="icon-style"></b-icon>
                {{ vista }}
            </button>
        </div>
    </div>
</template>

<script>
import StarRating from 'vue-star-rating'

export default {
    components: {
        StarRating
    },
    data() {
        return {
            pendiente: "Pendiente",
            seguir: "Seguir",
            vista: "Vista",
            clicked1: false,
            clicked2: false,
            clicked3: false,
            rating: 0,
            watcher: true,
        }
    },
    props: {
      audiovisual: {
        required: true,
        type: Object
      },
      usuarioActual: {
        required: true,
        type: Object
      },
      cambioAside: {
        type: Boolean
      }
    },
    created() {
        axios.get('/api/saber-seguimiento-audiovisual/', {
            params: { 
                audiovisual_id: this.audiovisual.id, 
                usuario_id: this.usuarioActual.id,
            }})
            .then(response => {
                if (response.data === 1) {
                    this.clicked1 = true;
                } else if (response.data === 2) {
                    this.clicked2 = true;
                    this.seguir = "Siguiendo";
                } else if (response.data === 3) {
                    this.clicked3 = true;
                }
            })
            .catch(error => console.log(error.response))
            .finally(() => this.loading = false);

        axios.get('/api/saber-valoracion-audiovisual/', {
            params: { 
                audiovisual_id: this.audiovisual.id, 
                usuario_id: this.usuarioActual.id,
            }})
            .then(response => {
                this.watcher = false;
                this.rating = response.data;
            })
            .catch(error => console.log(error.response))
            .finally(() => this.watcher = true);
    },
    methods: {
        seguimiento(tipo) {
            axios.post('/api/seguimiento-audiovisual/', 
            { 
                audiovisual_id: this.audiovisual.id, 
                usuario_id: this.usuarioActual.id, 
                tipo 
            })
            .then(response => {
                if (tipo === 1) {
                    if (response.data) {
                        this.clicked1 = true;
                        this.clicked2 = false;
                        this.seguir = "Seguir";
                        this.clicked3 = false;
                    } else
                        this.clicked1 = false;
                    this.$emit('comprobarCambioAside');
                } else if (tipo === 2) {
                    if (response.data) {
                        this.clicked1 = false;
                        this.clicked2 = true;
                        this.seguir = "Siguiendo";
                        this.clicked3 = false;
                    } else {
                        this.clicked2 = false;
                        this.seguir = "Seguir";
                    }
                } else if (tipo === 3) {
                    if (response.data) {
                        this.clicked1 = false;
                        this.clicked2 = false;
                        this.seguir = "Seguir";
                        this.clicked3 = true;
                    } else
                        this.clicked3 = false;
                    this.$emit('comprobarCambioAside');
                }

                this.clicked = true;
            })
            .catch(error => console.log(error.response));

            this.$emit('comprobarCambioResponsive');
        },
        borrarValoracion() {
            axios.post('/api/borrar-valoracion-audiovisual/', 
                { 
                    audiovisual_id: this.audiovisual.id, 
                    usuario_id: this.usuarioActual.id
                })
                .catch(error => console.log(error.response));

            this.rating = 0;

            this.$emit('actualizarValoracion');
            this.$emit('comprobarCambioResponsive');
        }
    },
    watch: {
        rating: function () {
            if (this.watcher) {
                axios.post('/api/valoracion-audiovisual/', 
                { 
                    audiovisual_id: this.audiovisual.id, 
                    usuario_id: this.usuarioActual.id, 
                    puntuacion: this.rating 
                })
                .catch(error => console.log(error.response));

                this.$emit('actualizarValoracion');
                this.$emit('comprobarCambioResponsive');
            } else
                this.watcher = true;
        }
    }
}
</script>
