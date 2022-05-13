<template>
    <div>
        <h4> Películas populares </h4>
        <lista-audiovisuales
            :audiovisuales="audiovisualesPopulares['peliculas']"
        />

        <h4> Series populares </h4>
        <lista-audiovisuales
            :audiovisuales="audiovisualesPopulares['series']"
        />
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
        }
    },
    created() {
        axios.get('/audiovisuales')
            .then(response => this.audiovisualesPopulares = response.data)
            .catch(error => { console.log(error.response) });
    },
}
</script>

<style>
    @import '/css/styles/styles.css';
</style>