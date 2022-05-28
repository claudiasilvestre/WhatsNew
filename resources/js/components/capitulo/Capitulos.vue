<template>
    <v-app>
        <div v-if="loading" class="d-flex justify-content-center flex-column align-items-center" style="height:40vh;">
            <b-spinner
                :variant="'light'"
                :key="'light'"
            ></b-spinner>
        </div>
        <div v-else class="d-flex justify-content-between list">
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
            <div class="d-flex align-items-start">
                <b-button variant="dark">
                    Marcar temporada como vista
                <b-icon icon="check-circle"></b-icon></b-button>
            </div>
        </div>
        <lista-capitulos :capitulos="capitulos" :idAudiovisual="audiovisual.id"/>
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
      }
    },
    data() {
        return {
            temporadas: [],
            selected: {},
            capitulos: [],
            loading: true,
        }
    },
    created() {
        axios.get('/api/temporadas/'+this.audiovisual.id)
            .then(response => this.temporadas = response.data)
            .catch(error => { console.log(error.response) })
            .finally(() => { 
                if (this.temporadas) this.selected = this.temporadas[0];

                axios.get('/api/capitulos/'+this.selected.id)
                    .then(response => this.capitulos = response.data)
                    .catch(error => { console.log(error.response) })
                    .finally(() => this.loading = false); 
            });
    },
    methods: {
      updateCapitulos(temporada) {
        axios.get('/api/capitulos/'+temporada.id)
            .then(response => this.capitulos = response.data)
            .catch(error => { console.log(error.response) });
      }
    }
}
</script>
