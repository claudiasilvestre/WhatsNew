<template>
    <div>
        <b-tabs>
            <b-tab title="Todo" active><audiovisuales-perfil :audiovisuales="todo" /></b-tab>
            <b-tab title="Series"><audiovisuales-perfil :audiovisuales="series" /></b-tab>
            <b-tab title="Películas"><audiovisuales-perfil :audiovisuales="peliculas" /></b-tab>
        </b-tabs>
    </div>
</template>

<script>
import AudiovisualesPerfil from './AudiovisualesPerfil.vue'

export default {
    components: {
        AudiovisualesPerfil
    },
    data() {
        return {
            todo: [],
            series: [],
            peliculas: []
        }
    },
    computed: {
        currentUser: {
            get() {
                return this.$store.state.currentUser.user;
            }
        }
    },
    created() {
        axios.get('/api/coleccion-usuario/'+this.currentUser.id)
            .then(response => {
                this.todo = response.data['todo'];
                this.series = response.data['series'];
                this.peliculas = response.data['peliculas'];
            })
            .catch(error => { console.log(error.response) });
    }
}
</script>