<template>
    <v-app>
        <div>
            <app-header />
            <div v-if="!loading" class="header">
                <img v-if="participante.foto" class="rounded" v-bind:src="participante.foto" v-bind:alt="participante.nombre" width="200" height="300">
                <img v-else class="rounded" src="/img/blank-profile-picture.jpg" v-bind:alt="participante.nombre" width="200" height="300">
                <div class="list">
                    <h3>{{ participante.nombre }}</h3>
                    <h5>Filmografía</h5>
                    <div class="d-flex flex-wrap">
                        <div v-for="audiovisual in audiovisuales" :key="audiovisual.id" class="mr-2">
                            <router-link :to="{ name: 'audiovisual', params: { id: audiovisual.audiovisual_id }}">
                                <img class="rounded" v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" width="150" height="220">
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </v-app>
</template>

<script>
import Header from './layouts/Header.vue'

export default {
    components: {
        'app-header': Header,
    },
    data() {
        return {
            idParticipante: this.$route.params.idPersona,
            participante: {},
            audiovisuales: [],
            loading: true,
        }
    },
    created() {
        axios.get('/api/personas/'+this.idParticipante)
            .then(response => this.participante = response.data[0])
            .catch(error => { console.log(error.response) });

        axios.get('/api/audiovisuales-participacion/'+this.idParticipante)
            .then(response => this.audiovisuales = response.data)
            .catch(error => { console.log(error.response) })
            .finally(() => this.loading = false);
    },
}
</script>