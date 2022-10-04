<template>
    <v-app>
        <div>
            <app-header />
            <div v-if="!loading" class="content header">
                <img v-if="participante.foto" class="rounded" v-bind:src="participante.foto" v-bind:alt="participante.nombre" height="280">
                <img v-else class="rounded" src="/img/blank-profile-picture.jpg" v-bind:alt="participante.nombre" height="280">
                <div class="list">
                    <h3>{{ participante.nombre }}</h3>
                    <h5>Filmografía</h5>
                    <div class="d-flex flex-wrap">
                        <div v-for="audiovisual in audiovisuales" :key="audiovisual.id" class="mr-2">
                            <router-link :to="{ name: 'audiovisual', params: { id: audiovisual.audiovisual_id }}">
                                <img class="rounded" v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" height="200">
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
            <app-footer />
        </div>
    </v-app>
</template>

<script>
import Header from './layouts/Header.vue'
import Footer from './layouts/Footer.vue'

export default {
    components: {
        'app-header': Header,
        'app-footer': Footer,
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
            .catch(error => { console.log(error.response) })
            .finally(() => document.title = this.participante.nombre + " - WhatsNew")

        axios.get('/api/audiovisuales-participacion/'+this.idParticipante)
            .then(response => this.audiovisuales = response.data)
            .catch(error => { console.log(error.response) })
            .finally(() => this.loading = false);
    }
}
</script>