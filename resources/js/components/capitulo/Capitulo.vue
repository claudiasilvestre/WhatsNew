<template>
    <v-app>
        <div>
            <app-header />
            <div v-if="Object.keys(currentUser).length === 0" class="d-flex justify-content-center flex-column align-items-center" style="height:40vh;">
                <b-spinner
                    :variant="'light'"
                    :key="'light'"
                ></b-spinner>
            </div>
            <div v-else-if="!loading" class="header">
                <aside-audiovisual :audiovisual="audiovisual" :currentUser="currentUser" />
                <div class="width">
                    <header-audiovisual :audiovisual="audiovisual" />
                    <div class="list">
                        <h3>{{ capitulo.nombre }}</h3>
                        <h5>Sinopsis</h5>
                        <p>{{ capitulo.sinopsis }}</p>
                        <h5>Comentarios</h5>
                    </div>
                    <comentarios :capitulo="capitulo" />
                </div>
            </div>
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import AsideAudiovisual from '../audiovisual/AsideAudiovisual.vue'
import HeaderAudiovisual from '../audiovisual/HeaderAudiovisual.vue'
import Comentarios from '../comentarios/Comentarios.vue'

export default {
    components: {
        'app-header': Header,
        AsideAudiovisual,
        HeaderAudiovisual,
        Comentarios
    },
    data() {
        return {
            idAudiovisual: this.$route.params.idAudiovisual,
            idCapitulo: this.$route.params.idCapitulo,
            audiovisual: {},
            capitulo: {},
            loading: true,
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
        axios.get('/api/audiovisuales/'+this.idAudiovisual)
            .then(response => this.audiovisual = response.data[0])
            .catch(error => { console.log(error.response) })
            .finally(() => this.loading = false);

        axios.get('/api/capitulo/'+this.idCapitulo)
            .then(response => this.capitulo = response.data[0])
            .catch(error => { console.log(error.response) });
    },
}
</script>