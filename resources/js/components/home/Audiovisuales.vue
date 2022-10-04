<template>
    <div class="content">
        <div v-if="loading" class="d-flex justify-content-center flex-column align-items-center" style="height:80vh;">
            <b-spinner
                :variant="'light'"
                :key="'light'"
            ></b-spinner>
        </div>
        <div v-else>
            <h5 class="titleAudiovisuales"> Últimas películas </h5>
            <lista-audiovisuales
                :audiovisuales="audiovisualesPopulares['peliculas']"
            />

            <h5 class="titleAudiovisuales"> Últimas series </h5>
            <lista-audiovisuales
                :audiovisuales="audiovisualesPopulares['series']"
            />

            <h5 class="titleAudiovisuales"> Recomendaciones </h5>
            <lista-audiovisuales v-if="this.audiovisualesRecomendados.length > 0"
                :audiovisuales="audiovisualesRecomendados"
            />
            <p v-else class="pl-5 pb-5">Marca alguna visualización para obtener recomendaciones.</p>
        </div>
    </div>
</template>

<script>
import ListaAudiovisuales from './ListaAudiovisuales.vue'

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