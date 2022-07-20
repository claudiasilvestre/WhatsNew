<template>
    <div>
        <img class="rounded" v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" width="200" height="300">
        <star-rating v-model="rating" :increment="0.5" :star-size="30" text-class="custom-text"></star-rating>
        <div v-if="!loading" class="buttons width">
            <button v-bind:class="{'btn btn-danger': !clicked1, 'btn btn-outline-danger': clicked1}" @click="seguimiento(1)" class="m-1"><b-icon icon="clock"></b-icon>
                {{ pendiente }}
            </button>
            <button v-bind:class="{'btn btn-warning': !clicked2, 'btn btn-outline-warning': clicked2}" @click="seguimiento(2)" class="m-1"><b-icon icon="eye"></b-icon>
                {{ seguir }}
            </button>
            <button v-bind:class="{'btn btn-info': !clicked3, 'btn btn-outline-info': clicked3}" @click="seguimiento(3)" class="m-1"><b-icon icon="check2"></b-icon>
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
            loading: true,
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
      currentUser: {
        required: true,
        type: Object
      },
      cambioAside: {
        required: true,
        type: Boolean
      }
    },
    created() {
        axios.get('/api/saber-seguimiento-audiovisual/', {
            params: { 
                audiovisual_id: this.audiovisual.id, 
                usuario_id: this.currentUser.id,
            }})
            .then(response => {
                if (response.data === 1) {
                    // this.pendiente = "Pendiente2";
                    this.clicked1 = true;
                } else if (response.data === 2) {
                    // this.seguir = "Seguir2";
                    this.clicked2 = true;
                } else if (response.data === 3) {
                    // this.vista = "Vista2";
                    this.clicked3 = true;
                }
            })
            .catch(error => console.log(error.response))
            .finally(() => this.loading = false);

        axios.get('/api/saber-valoracion-audiovisual/', {
            params: { 
                audiovisual_id: this.audiovisual.id, 
                usuario_id: this.currentUser.id,
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
                usuario_id: this.currentUser.id, 
                tipo 
            })
            .then(response => {
                if (tipo === 1) {
                    if (response.data) {
                        /* this.pendiente = "Pendiente2";
                        this.seguir = "Seguir";
                        this.vista = "Vista"; */
                        this.clicked1 = true;
                        this.clicked2 = false;
                        this.clicked3 = false;
                    } else
                        // this.pendiente = "Pendiente";
                        this.clicked1 = false;
                    this.$emit('comprobarCambioAside', !this.cambioAside);
                } else if (tipo === 2) {
                    if (response.data) {
                        /* this.pendiente = "Pendiente";
                        this.seguir = "Seguir2";
                        this.vista = "Vista"; */
                        this.clicked1 = false;
                        this.clicked2 = true;
                        this.clicked3 = false;
                    } else
                        // this.seguir = "Seguir";
                        this.clicked2 = false;
                } else if (tipo === 3) {
                    if (response.data) {
                        /* this.pendiente = "Pendiente";
                        this.seguir = "Seguir";
                        this.vista = "Vista2"; */
                        this.clicked1 = false;
                        this.clicked2 = false;
                        this.clicked3 = true;
                    } else
                        // this.vista = "Vista";
                        this.clicked3 = false;
                    this.$emit('comprobarCambioAside', !this.cambioAside);
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
                    usuario_id: this.currentUser.id, 
                    puntuacion: this.rating 
                })
                .then(response => {
                    console.log(response.data)
                })
                .catch(error => console.log(error.response));
            } else
                this.watcher = true;
        }
    }
}
</script>