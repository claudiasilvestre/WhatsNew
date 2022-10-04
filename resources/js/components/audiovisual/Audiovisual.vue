<template>
    <v-app>
        <div>
            <app-header />
            <div v-if="Object.keys(usuarioActual).length === 0" class="content d-flex justify-content-center flex-column align-items-center" style="height:40vh;">
                <b-spinner
                    :variant="'light'"
                    :key="'light'"
                ></b-spinner>
            </div>
            <div v-else-if="!loading" class="content header">
                <aside-audiovisual :audiovisual="audiovisual" :usuarioActual="usuarioActual" :cambioAside="cambioAside" @comprobarCambioAside="comprobarCambioAside" @actualizarValoracion="actualizarValoracion"/>
                <div class="width">
                    <header-audiovisual :audiovisual="audiovisual" :usuarioActual="usuarioActual" :cambioAside="cambioAside" @comprobarCambioAside="comprobarCambioAside" @actualizarValoracion="actualizarValoracion"/>
                    <menu-audiovisual :audiovisual="audiovisual" :cambioAside="cambioAside" />
                </div>
            </div>
            <app-footer />
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import Footer from '../layouts/Footer.vue'
import AsideAudiovisual from './AsideAudiovisual.vue'
import HeaderAudiovisual from './HeaderAudiovisual.vue'
import MenuAudiovisual from './MenuAudiovisual.vue'

export default {
    components: {
        'app-header': Header,
        'app-footer': Footer,
        AsideAudiovisual,
        HeaderAudiovisual,
        MenuAudiovisual,
    },
    data() {
        return {
            id: this.$route.params.id,
            audiovisual: {},
            loading: true,
            cambioAside: false,
        }
    },
    computed: {
        usuarioActual: {
            get() {
                return this.$store.state.usuarioActual.usuario;
            }
        }
    },
    created() {
        axios.get('/api/audiovisuales/'+this.id)
            .then(response => this.audiovisual = response.data[0])
            .catch(error => { console.log(error.response) })
            .finally(() => {
                this.loading = false
                document.title = this.audiovisual.titulo + " - WhatsNew"
            });
    },
    methods: {
      comprobarCambioAside() {
        this.cambioAside = !this.cambioAside;
      },
      actualizarValoracion() {
        axios.get('/api/audiovisuales/'+this.id)
            .then(response => this.audiovisual = response.data[0])
            .catch(error => { console.log(error.response) });
      },
    }
}
</script>