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
                <aside-audiovisual :key="cambioResponsive" :audiovisual="audiovisual" :usuarioActual="usuarioActual" :cambioAside="cambioAside" @comprobarCambioAside="comprobarCambioAside" @comprobarCambioAside2="comprobarCambioAside2" @actualizarValoracion="actualizarValoracion"/>
                <div class="width">
                    <header-audiovisual :audiovisual="audiovisual" :usuarioActual="usuarioActual" :cambioAside="cambioAside" :cambioAside2="cambioAside2" @comprobarCambioAside="comprobarCambioAside" @comprobarCambioResponsive="comprobarCambioResponsive" @actualizarValoracion="actualizarValoracion"/>
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
            cambioAside2: false,
            cambioResponsive: false,
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
      comprobarCambioAside2() {
        this.cambioAside2 = !this.cambioAside2;
      },
      comprobarCambioResponsive() {
        this.cambioResponsive = !this.cambioResponsive;
      },
      actualizarValoracion() {
        axios.get('/api/audiovisuales/'+this.id)
            .then(response => this.audiovisual = response.data[0])
            .catch(error => { console.log(error.response) });
      },
    }
}
</script>