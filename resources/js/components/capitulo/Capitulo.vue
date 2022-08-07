<template>
    <v-app>
        <div>
            <app-header />
            <div v-if="Object.keys(currentUser).length === 0" class="content d-flex justify-content-center flex-column align-items-center" style="height:40vh;">
                <b-spinner
                    :variant="'light'"
                    :key="'light'"
                ></b-spinner>
            </div>
            <div v-else-if="!loading" class="content header">
                <aside-audiovisual :audiovisual="audiovisual" :currentUser="currentUser" />
                <div class="width">
                    <header-audiovisual :audiovisual="audiovisual" />
                    <div class="list">
                        <p class="pointer" @click="retroceder()"><b-icon icon="arrow-left"></b-icon> Capítulos</p>
                        <p v-if="!anteriorCapitulo_id && siguienteCapitulo_id" class="d-flex justify-content-end">
                            <span v-if="anteriorCapitulo_id" class="pointer" @click="anteriorCapitulo()"><b-icon icon="arrow-left-short"></b-icon>Anterior capítulo</span>
                            <span v-if="siguienteCapitulo_id" class="pointer" @click="siguienteCapitulo()">Siguiente capítulo<b-icon icon="arrow-right-short"></b-icon></span>
                        </p>
                        <p v-else class="d-flex justify-content-between">
                            <span v-if="anteriorCapitulo_id" class="pointer" @click="anteriorCapitulo()"><b-icon icon="arrow-left-short"></b-icon>Anterior capítulo</span>
                            <span v-if="siguienteCapitulo_id" class="pointer" @click="siguienteCapitulo()">Siguiente capítulo<b-icon icon="arrow-right-short"></b-icon></span>
                        </p>
                        <div class="d-flex justify-content-between">
                            <h3>{{ capitulo.nombre }}</h3>
                            <button v-bind:class="{'btn btn-info': !clicked, 'btn btn-outline-info': clicked}" @click="seguimiento()" class="m-1"><b-icon icon="check2"></b-icon>
                                Visto
                            </button>
                        </div>
                        <h5>Sinopsis</h5>
                        <p>{{ capitulo.sinopsis }}</p>
                        <h5>Comentarios</h5>
                    </div>
                    <comentarios :capitulo="capitulo" />
                </div>
            </div>
            <app-footer />
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import Footer from '../layouts/Footer.vue'
import AsideAudiovisual from '../audiovisual/AsideAudiovisual.vue'
import HeaderAudiovisual from '../audiovisual/HeaderAudiovisual.vue'
import Comentarios from '../comentarios/Comentarios.vue'

export default {
    components: {
        'app-header': Header,
        'app-footer': Footer,
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
            clicked: false,
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
            .catch(error => { console.log(error.response) })
            .finally(() => document.title = this.audiovisual.titulo + ": " + this.capitulo.nombre + " - What's new");

        axios.get('/api/capitulos-anterior-siguiente/', {
                    params: { 
                        capitulo_id: this.idCapitulo, 
                        audiovisual_id: this.idAudiovisual,
                    }})
                    .then(response => {
                        this.anteriorCapitulo_id = response.data['anteriorCapitulo_id'];
                        this.siguienteCapitulo_id = response.data['siguienteCapitulo_id'];
                    })
                    .catch(error => console.log(error.response));

        axios.get('/api/saber-visualizacion-capitulo/'+this.idCapitulo)
            .then(response => this.clicked = response.data)
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
        },
        seguimiento() {
            axios.post('/api/visualizacion-capitulo/'+this.idCapitulo)
            .then(response => {
                this.clicked = response.data;
            })
            .catch(error => console.log(error.response));
        }
    }
}
</script>