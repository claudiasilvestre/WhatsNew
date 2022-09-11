<template>
    <div class="mt-4 perfil-activity">
        <div v-for="actividad in actividadTotal" :key="actividad.id" class="p-2 mt-2 mb-2 rounded background2 d-flex justify-content-between">
            <div v-if="actividad.tipoAudiovisual_id" class="d-flex flex-column">
                <div class="d-flex flex-row">
                    <img class="roundedPerfil m-2" v-bind:src="usuario.foto" v-bind:alt="usuario.nombre" width="45" height="45">
                    <div class="m-2">
                        <span>{{ usuario.nombre }}</span>
                        <span v-if="actividad.tipo === 1"> ha marcado como pendiente </span>
                        <span v-else-if="actividad.tipo === 2"> sigue </span>
                        <span v-else-if="actividad.tipo === 3"> ha visto </span>
                        <span v-if="actividad.tipoAudiovisual_id === 1">la película </span>
                        <span v-else-if="actividad.tipoAudiovisual_id === 2">la serie </span>
                        <router-link :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual }}">
                            <span>{{ actividad.titulo_audiovisual }}</span>
                        </router-link>
                    </div>
                </div>
                <span class="p-letra">{{ moment(actividad.created_at).format('LL') }}</span>
            </div>
            <div v-else-if="actividad.numero_capitulo" class="d-flex flex-column">
                <div class="d-flex flex-row">
                    <img class="roundedPerfil m-2" v-bind:src="usuario.foto" v-bind:alt="usuario.nombre" width="45" height="45">
                    <span class="m-2">{{ usuario.nombre }} ha visto el capítulo 
                    <router-link :to="{ name: 'capitulo', params: { idAudiovisual: actividad.id_audiovisual_capitulo, idCapitulo: actividad.id_capitulo }}">
                        {{ actividad.numero_temporada }}x{{ actividad.numero_capitulo }} - {{ actividad.nombre }} 
                    </router-link> de 
                    <router-link :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual_capitulo }}">
                        {{ actividad.titulo_audiovisual_capitulo }}
                    </router-link></span>
                </div>
                <span class="p-letra">{{ moment(actividad.created_at).format('LL') }}</span>
            </div>
            <div v-else-if="actividad.numero_temporada_actividad" class="d-flex flex-column">
                <div class="d-flex flex-row">
                    <img class="roundedPerfil m-2" v-bind:src="usuario.foto" v-bind:alt="usuario.nombre" width="45" height="45">
                    <span class="m-2">{{ usuario.nombre }} ha visto la temporada {{ actividad.numero_temporada_actividad }} 
                    de 
                    <router-link :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual_temporada }}">
                        {{ actividad.titulo_audiovisual_temporada }}
                    </router-link></span>
                </div>
                <span class="p-letra">{{ moment(actividad.created_at).format('LL') }}</span>
            </div>
            <div class="d-flex flex-row">
                <div class="d-flex align-items-center">
                    <router-link v-if="actividad.id_audiovisual" :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual }}">
                        <img class="rounded p-2" v-bind:src="actividad.audiovisual_cartel" v-bind:alt="actividad.titulo_audiovisual" width="55" height="80">
                    </router-link>
                    
                    <router-link v-if="actividad.id_audiovisual_capitulo" :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual_capitulo }}">
                        <img class="rounded p-2" v-bind:src="actividad.capitulo_cartel" v-bind:alt="actividad.titulo_audiovisual_capitulo" width="55" height="80">
                    </router-link>

                    <router-link v-if="actividad.id_audiovisual_temporada" :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual_temporada }}">
                        <img class="rounded p-2" v-bind:src="actividad.temporada_cartel" v-bind:alt="actividad.titulo_audiovisual_temporada" width="55" height="80">
                    </router-link>
                </div>
                <b-icon v-if="Number(usuario_id) === usuarioActual.id" icon="x-circle" variant="danger" @click="borrarActividad(actividad.id)" class="pointer"></b-icon>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";

export default {
    data() {
        return {
            actividadTotal: [],
            usuario_id: this.$route.params.idPersona,
            moment: moment,
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
        moment.locale('es');
        
        axios.get('/api/personas/'+this.usuario_id)
            .then(response => this.usuario = response.data[0])
            .catch(error => { console.log(error.response) });

        axios.get('/api/actividad-usuario/'+this.usuario_id)
            .then(response => {
                this.actividadTotal = response.data;
            })
            .catch(error => { console.log(error.response) });
    },
    methods: {
        borrarActividad(actividad_id) {
            axios.post('/api/borrar-actividad/'+actividad_id)
            .catch((errors) => {
                console.log(errors.response)
            });

            axios.get('/api/actividad-usuario/'+this.usuario_id)
            .then(response => {
                this.actividadTotal = response.data;
            })
            .catch(error => { console.log(error.response) });

            this.$emit('cambio');
        }
    }
}
</script>