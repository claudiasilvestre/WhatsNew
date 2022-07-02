<template>
    <v-app>
        <div>
            <app-header />
            <h3>Resultados para la búsqueda "{{ busqueda }}"</h3>
            <div class="d-flex flex-wrap list">
                <div v-for="usuario in usuarios" :key="usuario.id" class="mr-2">
                    <router-link :to="{ name: 'perfil', params: { idPersona: usuario.id }}">
                        <img class="rounded" v-bind:src="usuario.foto" v-bind:alt="usuario.nombre" width="175" height="250">
                    </router-link>
                </div>
                <div v-for="participante in participantes" :key="participante.id" class="mr-2">
                    <router-link :to="{ name: 'informacion', params: { idPersona: participante.id }}">
                        <img class="rounded" v-bind:src="participante.foto" v-bind:alt="participante.nombre" width="175" height="250">
                    </router-link>
                </div>
                <div v-for="audiovisual in audiovisuales" :key="audiovisual.id" class="mr-2">
                    <router-link :to="{ name: 'audiovisual', params: { id: audiovisual.id }}">
                        <img class="rounded" v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" width="175" height="250">
                    </router-link>
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
            busqueda: this.$route.params.busqueda,
            usuarios: [],
            participantes: [],
            audiovisuales: [],
        }
    },
    created() {
        axios.get('/api/search/'+this.busqueda)
            .then(response => {
                this.usuarios = response.data['usuarios'];
                this.participantes = response.data['participantes'];
                this.audiovisuales = response.data['audiovisuales'];
            })
            .catch(error => console.log(error.response))
    },
}
</script>