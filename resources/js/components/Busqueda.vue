<template>
    <v-app>
        <div>
            <app-header />
            <div class="content list">
                <h3 class="title-style" style="font-size:xx-large">Resultados para la búsqueda "{{ busqueda }}"</h3>
                <div class="d-flex flex-wrap">
                    <div v-for="usuario in usuarios" :key="usuario.id" class="mr-2">
                        <router-link :to="{ name: 'perfil', params: { idPersona: usuario.id }}">
                            <div class="d-flex flex-column">
                                <img class="rounded" v-bind:src="usuario.foto" v-bind:alt="usuario.nombre" width="140" height="140">
                                <span>{{ usuario.nombre }}</span>
                                <span style="font-size:13px">Usuario: {{ usuario.usuario }}</span>
                            </div>
                        </router-link>
                    </div>
                    <div v-for="participante in participantes" :key="participante.id" class="mr-2">
                        <router-link :to="{ name: 'informacion', params: { idPersona: participante.id }}">
                            <div class="d-flex flex-column">
                                <img v-if="participante.foto" class="rounded" v-bind:src="participante.foto" v-bind:alt="participante.nombre" width="140" height="215">
                                <img v-else class="rounded" src="/img/blank-profile-picture.jpg" v-bind:alt="participante.nombre" width="140" height="215">
                                <span>{{ participante.nombre }}</span>
                            </div>
                        </router-link>
                    </div>
                    <div v-for="audiovisual in audiovisuales" :key="audiovisual.id" class="mr-2">
                        <router-link :to="{ name: 'audiovisual', params: { id: audiovisual.id }}">
                            <div class="d-flex flex-column">
                                <img class="rounded" v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" width="140" height="215">
                                <span>{{ audiovisual.titulo }}</span>
                                <span>({{ audiovisual.anno }})</span>
                            </div>
                        </router-link>
                    </div>
                </div>
                <p v-if="this.usuarios.length === 0 && this.participantes.length === 0 && this.audiovisuales.length === 0"> 
                    No se han encontrado resultados para la búsqueda.
                </p>
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
            busqueda: this.$route.params.busqueda,
            usuarios: [],
            participantes: [],
            audiovisuales: [],
        }
    },
    created() {
        axios.get('/api/busqueda/'+this.busqueda)
            .then(response => {
                this.usuarios = response.data['usuarios'];
                this.participantes = response.data['participantes'];
                this.audiovisuales = response.data['audiovisuales'];
            })
            .catch(error => console.log(error.response))
    },
    mounted() {
        document.title = "“" + this.busqueda + "”" + " - What's new"
    }
}
</script>