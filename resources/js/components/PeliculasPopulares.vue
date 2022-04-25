<template>
    <div> 
        <h2> Películas populares </h2>
        <div v-for="peliculaPopular in peliculasPopulares" :key="peliculaPopular.id">
            {{ peliculaPopular.titulo }}
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            peliculasPopulares: []
        }
    },
    created() {
        axios.get('/audiovisuales')
            .then(response => this.peliculasPopulares = response.data)
            .catch(error => { console.log(error.response) });
    }
}
</script>

<style>

</style>