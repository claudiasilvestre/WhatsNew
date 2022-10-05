<template>
    <div>
        <star-rating v-model="rating" :increment="0.5" :star-size="25" text-class="custom-text" class="m-2"></star-rating>
        <div>
            <button v-bind:class="{'btn btn-danger': !clicked1, 'btn btn-outline-danger': clicked1}" @click="seguimiento(1)" class="m-1"><b-icon icon="clock"></b-icon>
                {{ pendiente }}
            </button>
            <button v-if="audiovisual.tipoAudiovisual_id === 2" v-bind:class="{'btn btn-warning': !clicked2, 'btn btn-outline-warning': clicked2}" @click="seguimiento(2)" class="m-1"><b-icon icon="eye"></b-icon>
                {{ seguir }}
            </button>
            <button v-bind:class="{'btn btn-light': !clicked3, 'btn btn-outline-light': clicked3}" @click="seguimiento(3)" class="m-1"><b-icon icon="check2"></b-icon>
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
                        this.clicked3 = false;
                    } else
                        this.clicked1 = false;
                    this.$emit('comprobarCambioAside');
                } else if (tipo === 2) {
                    if (response.data) {
                        this.clicked1 = false;
                        this.clicked2 = true;
                        this.clicked3 = false;
                    } else
                        this.clicked2 = false;
                } else if (tipo === 3) {
                    if (response.data) {
                        this.clicked1 = false;
                        this.clicked2 = false;
                        this.clicked3 = true;
                    } else
                        this.clicked3 = false;
                    this.$emit('comprobarCambioAside');
                }

                this.clicked = true;
            })
            .catch(error => console.log(error.response));
        },
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
            } else
                this.watcher = true;
        }
    }
}
</script>
