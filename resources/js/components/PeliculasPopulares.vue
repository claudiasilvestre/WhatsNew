<template>
    <div> 
        <h3> Películas populares </h3>
        <div class=card>
            <div v-for="peliculaPopular in audiovisualesPopulares['peliculas']" :key="peliculaPopular.id">
                <img :src="peliculaPopular.cartel" :alt="peliculaPopular.titulo" width="200" height="270">
            </div>
        </div>

        <h3> Series populares </h3>
        <div class=card>
            <div v-for="seriePopular in audiovisualesPopulares['series']" :key="seriePopular.id">
                <img :src="seriePopular.cartel" :alt="seriePopular.titulo" width="200" height="270">
            </div>
        </div>
    </div>
</template>

<script>

export default {
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
    @import './assets/styles/styles.css';
</style>