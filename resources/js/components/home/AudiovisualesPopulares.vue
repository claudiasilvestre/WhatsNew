<template>
    <div>
        <div v-if="loading" class="d-flex justify-content-center flex-column align-items-center" style="height:80vh;">
            <b-spinner
                :variant="'light'"
                :key="'light'"
            ></b-spinner>
        </div>
        <div v-else>
            <h4> Películas populares </h4>
            <lista-audiovisuales
                :audiovisuales="audiovisualesPopulares['peliculas']"
            />

            <h4> Series populares </h4>
            <lista-audiovisuales
                :audiovisuales="audiovisualesPopulares['series']"
            />

            <h4 v-if="this.audiovisualesRecomendados.length > 0"> Recomendaciones </h4>
            <lista-audiovisuales v-if="this.audiovisualesRecomendados.length > 0"
                :audiovisuales="audiovisualesRecomendados"
            />
        </div>
    </div>
</template>

<script>
import ListaAudiovisuales from '../ListaAudiovisuales.vue'

export default {
    components: {
        ListaAudiovisuales
    },
    data() {
        return {
            audiovisualesPopulares: [],
            audiovisualesRecomendados: [],
            loading: true,
        }
    },
    created() {
        axios.get('/api/audiovisuales')
            .then(response => this.audiovisualesPopulares = response.data)
            .catch(error => { console.log(error.response) })
            .finally(() => this.loading = false);

        axios.get('/api/recomendaciones')
            .then(response => this.audiovisualesRecomendados = response.data)
            .catch(error => { console.log(error.response) });
    },
}
</script>

<style>
    @import '/css/styles/styles.css';
</style>