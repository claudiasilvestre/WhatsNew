<template>
    <v-app>
        <div v-if="loading" class="d-flex justify-content-center flex-column align-items-center" style="height:40vh;">
            <b-spinner
                :variant="'light'"
                :key="'light'"
            ></b-spinner>
        </div>
        <div v-else class="header-capitulos listTemporada">
            <div>
                <v-select
                    v-model="selected"
                    :items="temporadas"
                    :background-color="'#1e1e1e'"
                    item-text="nombre"
                    return-object
                    solo
                    v-on:change="updateCapitulos(selected)"
                />
            </div>
            <div>
                <button v-bind:class="{'btn btn-outline-light': !state, 'btn btn-light': state}" @click="vista(selected.id)" class="m-1">
                    Marcar temporada como vista
                <b-icon icon="check-circle"></b-icon></button>
            </div>
        </div>
        <lista-capitulos :capitulos="capitulos" :idAudiovisual="audiovisual.id" :temporada="selected" :vista="clicked" :cambio="cambio" :noCambio="noCambio" :cambioAside="cambioAside" @comprobarVista="comprobarVista" />
    </v-app>
</template>

<script>
import ListaCapitulos from './ListaCapitulos.vue'

export default {
    components: {
        ListaCapitulos
    },
    props: {
      audiovisual: {
        required: true,
        type: Object
      },
      cambioAside: {
        type: Boolean
      }
    },
    data() {
        return {
            temporadas: [],
            selected: {},
            capitulos: [],
            loading: true,
            clicked: false,
            state: false,
            cambio: true,
            noCambio: true,
        }
    },
    computed: {
        usuarioActual: {
            get() {
                return this.$store.state.usuarioActual.usuario;
            }
        }
    },
    watch: {
        cambioAside: function () {
            axios.get('/api/saber-visualizacion-temporada/', {
                    params: { 
                        temporada_id: this.selected.id, 
                        usuario_id: this.usuarioActual.id,
                    }})
                    .then(response => {
                        if (response.data) {
                            this.state = true;
                            this.clicked = true;
                        } else {
                            this.state = false;
                            this.clicked = false;
                        }
                    })
                    .catch(error => console.log(error.response))
        }
    },
    created() {
        axios.get('/api/primera-temporada/'+this.audiovisual.id)
            .then(response => this.selected = response.data)
            .catch(error => { console.log(error.response) })
            .finally(() => {
                axios.get('/api/capitulos/'+this.selected.id)
                    .then(response => this.capitulos = response.data)
                    .catch(error => { console.log(error.response) })

                axios.get('/api/saber-visualizacion-temporada/', {
                    params: { 
                        temporada_id: this.selected.id, 
                        usuario_id: this.usuarioActual.id,
                    }})
                    .then(response => {
                        if (response.data) {
                            this.state = true;
                            this.clicked = true;
                        }
                    })
                    .catch(error => console.log(error.response))
                    .finally(() => this.loading = false)
            }); 

        axios.get('/api/temporadas/'+this.audiovisual.id)
            .then(response => this.temporadas = response.data)
            .catch(error => { console.log(error.response) })
    },
    methods: {
      updateCapitulos(temporada) {
        axios.get('/api/capitulos/'+temporada.id)
            .then(response => this.capitulos = response.data)
            .catch(error => { console.log(error.response) });

        axios.get('/api/saber-visualizacion-temporada/', {
                params: { 
                    temporada_id: this.selected.id, 
                    usuario_id: this.usuarioActual.id,
                }})
                .then(response => {
                    if (response.data) {
                        this.state = true;
                        this.clicked = true;
                    } else {
                        this.state = false;
                        this.clicked = false;
                    }
                })
                .catch(error => console.log(error.response))
      },
      vista(temporada_id) {
        axios.post('/api/visualizacion-temporada/', 
            { 
                temporada_id, 
                usuario_id: this.usuarioActual.id,
                capitulos: this.capitulos
            })
            .then(response => {
                if (response.data) {
                    this.state = true;
                    this.clicked = true;
                } else {
                    this.state = false;
                    this.clicked = false;
                }
                this.noCambio = true;
            })
            .catch(error => console.log(error.response));
      },
      comprobarVista(cambio) {
        this.state = cambio;
        this.cambio = cambio;
        this.noCambio = false;
        if (!cambio)
            this.clicked = false;
      }
    }
}
</script>
