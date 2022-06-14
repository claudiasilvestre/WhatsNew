<template>
    <v-app>
        <div>
            <app-header />
            <div v-if="!loading" class="header">
                <aside-audiovisual :audiovisual="audiovisual" />
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
import Comentarios from '../Comentarios.vue'

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
    created() {
        axios.get('/api/audiovisuales/'+this.idAudiovisual)
            .then(response => this.audiovisual = response.data[0])
            .catch(error => { console.log(error.response) });

        axios.get('/api/capitulo/'+this.idCapitulo)
            .then(response => this.capitulo = response.data[0])
            .catch(error => { console.log(error.response) })
            .finally(() => this.loading = false);
    },
}
</script>