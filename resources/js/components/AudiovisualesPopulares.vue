<template>
    <div> 
        <h3> Películas populares </h3>
        <lista-audiovisuales
            :audiovisuales="audiovisualesPopulares['peliculas']"
        />

        <h3> Series populares </h3>
        <lista-audiovisuales
            :audiovisuales="audiovisualesPopulares['series']"
        />
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
            audiovisualesPopulares: []
        }
    },
    created() {
        axios.get('/audiovisuales')
            .then(response => this.audiovisualesPopulares = response.data)
            .catch(error => { console.log(error.response) });
    }
}
</script>

<style>
    @import '/css/styles/styles.css';
</style>