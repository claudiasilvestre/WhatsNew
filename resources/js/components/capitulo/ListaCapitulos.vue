<template>
    <div>
        <div v-for="(capitulo, index) in capitulos" :key="capitulo.id" class="list-capitulos rounded background2 mb-2">
            <router-link :to="{ name: 'capitulo', params: { idAudiovisual: idAudiovisual, idCapitulo: capitulo.id }}">
                <img class="rounded ml-4" v-bind:src="capitulo.cartel" v-bind:alt="capitulo.nombre" width="250" height="140">
            </router-link>
            <div class="w-100 p-4 d-flex flex-row justify-content-between">
                <div class="w-100 d-flex flex-column justify-content-between">
                    <div>
                        <router-link :to="{ name: 'capitulo', params: { idAudiovisual: idAudiovisual, idCapitulo: capitulo.id }}">
                            <h5>{{ temporada.numero }}x{{ capitulo.numero }} - {{ capitulo.nombre }}</h5>
                        </router-link>
                        <p>{{ capitulo.sinopsis }}</p>
                    </div>
                    <span style="color:#d4d4d4; font-size:14px">{{ moment(capitulo.fechaLanzamiento).format('LL') }}</span>
                </div>
                <div class="pl-4 d-flex align-items-center">
                    <button type="button" v-if="!loading" v-bind:class="{'color-a': clicked[index]}" @click="visto(capitulo.id, index)" class="m-1 h4">
                        <b-icon icon="check-circle" class="icon-caps"></b-icon>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";

export default {
    props: {
      capitulos: {
        required: true,
        type: Array
      },
      idAudiovisual: {
        required: true,
        type: Number
      },
      temporada: {
        required: true,
        type: Object
      },
      vista: {
        required: true,
        type: Boolean
      },
      cambio: {
        required: true,
        type: Boolean
      },
      noCambio: {
        required: true,
        type: Boolean
      },
      cambioAside: {
        required: true,
        type: Boolean
      },
    },
    data() {
        return {
            clicked: [],
            loading: false,
            moment: moment,
        }
    },
    created() {
        moment.locale('es');
    },
    computed: {
        usuarioActual: {
            get() {
                return this.$store.state.usuarioActual.usuario;
            }
        }
    },
    watch: {
        capitulos: function () {
            axios.get('/api/visualizaciones/', {
                params: { 
                    capitulos: this.capitulos,
                    usuario_id: this.usuarioActual.id,
                }})
                .then(response => {
                    this.clicked = response.data;
                })
                .catch(error => console.log(error.response))
        },
        vista: function () {
            if (this.vista)
                this.clicked = Array(this.capitulos.length).fill(true);
            else {
                if (this.noCambio)
                    this.clicked = Array(this.capitulos.length).fill(false);
            }
        },
        cambio: function () {
            if (this.cambio)
                this.clicked = Array(this.capitulos.length).fill(true);
            else {
                this.loading = true;
                axios.get('/api/visualizaciones/', {
                    params: { 
                        capitulos: this.capitulos,
                        usuario_id: this.usuarioActual.id,
                    }})
                    .then(response => {
                        this.clicked = response.data;
                    })
                    .catch(error => console.log(error.response))
                    .finally(() => this.loading = false);
            }
        },
        cambioAside: function () {
            if (this.capitulos.length > 0) {
                axios.get('/api/visualizaciones/', {
                    params: { 
                        capitulos: this.capitulos,
                        usuario_id: this.usuarioActual.id,
                    }})
                    .then(response => {
                        this.clicked = response.data;
                    })
                    .catch(error => console.log(error.response));
            }
        },
    },
    methods: {
        visto(capitulo_id, index) {
            axios.post('/api/visualizacion-capitulo/'+capitulo_id)
            .then(response => {
                this.loading = true;
                if (response.data['estado']) {
                    this.clicked[index] = response.data['estado'];
                    if (response.data['cambio'])
                        this.$emit('comprobarVista', true);
                }
                else {
                    this.clicked[index] = response.data['estado'];
                    if (response.data['cambio'])
                        this.$emit('comprobarVista', false);
                }
            })
            .catch(error => console.log(error.response))
            .finally(() => this.loading = false);
        },
    }
}
</script>