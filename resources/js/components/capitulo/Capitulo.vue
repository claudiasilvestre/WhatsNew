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
                        <p class="pointer" @click="retroceder()"><b-icon icon="arrow-left"></b-icon> Capítulos</p>
                        <p class="d-flex justify-content-between">
                            <span class="pointer" @click="anteriorCapitulo()"><b-icon icon="arrow-left-short"></b-icon>Anterior capítulo</span>
                            <span class="pointer" @click="siguienteCapitulo()">Siguiente capítulo<b-icon icon="arrow-right-short"></b-icon></span>
                        </p>
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
            anteriorCapitulo_id: '',
            siguienteCapitulo_id: '',
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

        axios.get('/api/capitulos-anterior-siguiente/'+this.idCapitulo)
            .then(response => {
                this.anteriorCapitulo_id = response.data['anteriorCapitulo'];
                this.siguienteCapitulo_id = response.data['siguienteCapitulo'];
            })
            .catch(error => { console.log(error.response) });
    },
    methods: {
        retroceder() {
            this.$router.push('/media/'+this.audiovisual.id)
        },
        anteriorCapitulo() {
            if (this.anteriorCapitulo_id)
                this.$router.push('/media/'+this.audiovisual.id+'/episode/'+this.anteriorCapitulo_id)
        },
        siguienteCapitulo() {
            if (this.siguienteCapitulo_id)
                this.$router.push('/media/'+this.audiovisual.id+'/episode/'+this.siguienteCapitulo_id)
        }
    }
}
</script>