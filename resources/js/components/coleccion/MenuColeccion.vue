<template>
    <div>
        <b-tabs v-if="tipo === 2">
            <b-tab title="Siguiendo" active><lista-coleccion :audiovisuales="series_siguiendo" /></b-tab>
            <b-tab title="Vistas"><lista-coleccion :audiovisuales="series_vistas" /></b-tab>
            <b-tab title="Pendientes"><lista-coleccion :audiovisuales="series_pendientes" /></b-tab>
            <b-tab title="Todo"><lista-coleccion :audiovisuales="series" /></b-tab>
        </b-tabs>
        <b-tabs v-else-if="tipo === 1">
            <b-tab title="Vistas"><lista-coleccion :audiovisuales="peliculas_vistas" /></b-tab>
            <b-tab title="Pendientes"><lista-coleccion :audiovisuales="peliculas_pendientes" /></b-tab>
            <b-tab title="Todo"><lista-coleccion :audiovisuales="peliculas" /></b-tab>
        </b-tabs>
    </div>
</template>

<script>
import ListaColeccion from '../ListaColeccion.vue'

export default {
    components: {
        ListaColeccion
    },
    props: {
        tipo: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            series: [],
            series_pendientes: [],
            series_siguiendo: [],
            series_vistas: [],
            peliculas: [],
            peliculas_pendientes: [],
            peliculas_vistas: [],
        }
    },
    created() {
        axios.get('/api/mi-coleccion')
            .then(response => {
                this.series = response.data['series'];
                this.series_pendientes = response.data['series_pendientes'];
                this.series_siguiendo = response.data['series_siguiendo'];
                this.series_vistas = response.data['series_vistas'];
                this.peliculas = response.data['peliculas'];
                this.peliculas_pendientes = response.data['peliculas_pendientes'];
                this.peliculas_vistas = response.data['peliculas_vistas'];
            })
            .catch(error => { console.log(error.response) });
    }
}
</script>