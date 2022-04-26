<template>
    <div> 
        <h3> Películas populares </h3>
        <div v-for="peliculaPopular in audiovisualesPopulares['peliculas']" :key="peliculaPopular.id" class=card>
            <img v-bind:src="peliculaPopular.cartel" alt="" width="200" height="300">
        </div>

        <h3> Series populares </h3>
        <div v-for="seriePopular in audiovisualesPopulares['series']" :key="seriePopular.id" class=card>
            <img v-bind:src="seriePopular.cartel" alt="" width="200" height="300">
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