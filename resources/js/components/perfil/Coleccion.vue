<template>
    <div>
        <b-tabs>
            <b-tab title="Todo" active><lista-coleccion :audiovisuales="todo" /></b-tab>
            <b-tab title="Series"><lista-coleccion :audiovisuales="series" /></b-tab>
            <b-tab title="Películas"><lista-coleccion :audiovisuales="peliculas" /></b-tab>
        </b-tabs>
    </div>
</template>

<script>
import ListaColeccion from '../ListaColeccion.vue'

export default {
    components: {
        ListaColeccion
    },
    data() {
        return {
            todo: [],
            series: [],
            peliculas: [],
            usuario_id: this.$route.params.idPersona,
        }
    },
    created() {
        axios.get('/api/coleccion-usuario/'+this.usuario_id)
            .then(response => {
                this.todo = response.data['todo'];
                this.series = response.data['series'];
                this.peliculas = response.data['peliculas'];
            })
            .catch(error => { console.log(error.response) });
    }
}
</script>