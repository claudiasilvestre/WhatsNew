<template>
    <div>
        <div v-for="(capitulo, index) in capitulos" :key="capitulo.id" class="list d-flex flex-row background2 mb-2">
            <router-link :to="{ name: 'capitulo', params: { idAudiovisual: idAudiovisual, idCapitulo: capitulo.id }}">
                <img class="rounded" v-bind:src="capitulo.cartel" v-bind:alt="capitulo.nombre" width="250" height="140">
            </router-link>
            <div class="w-100 pl-4">
                <router-link :to="{ name: 'capitulo', params: { idAudiovisual: idAudiovisual, idCapitulo: capitulo.id }}">
                    <h5>{{ capitulo.nombre }}</h5>
                </router-link>
                <p>{{ capitulo.sinopsis }}</p>
                <div class="d-flex justify-content-end">
                    <button v-if="!loading" v-bind:class="{'btn btn-light': !clicked[index], 'btn btn-danger': clicked[index]}" @click="visto(capitulo.id, index)" class="m-1">
                        Visto
                    <b-icon icon="check-circle"></b-icon></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
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
    },
    data() {
        return {
            clicked: [],
            loading: false,
        }
    },
    watch: {
        capitulos: function () {
            this.loading = true;
            axios.get('/api/visualizaciones/', {
                params: { 
                    capitulos: this.capitulos,
                    usuario_id: 1,
                }})
                .then(response => {
                    this.clicked = response.data;
                })
                .catch(error => console.log(error.response))
                .finally(() => this.loading = false);
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
                        usuario_id: 1,
                    }})
                    .then(response => {
                        this.clicked = response.data;
                    })
                    .catch(error => console.log(error.response))
                    .finally(() => this.loading = false);
            }
        }
    },
    methods: {
        visto(capitulo_id, index) {
            axios.post('/api/visualizacion-capitulo/', 
            { 
                capitulo_id, 
                usuario_id: 1,
            })
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