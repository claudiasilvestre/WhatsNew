<template>
    <div v-if="loading" class="d-flex justify-content-center flex-column align-items-center" style="height:40vh;">
        <b-spinner
            :variant="'light'"
            :key="'light'"
        ></b-spinner>
    </div>
    <div v-else class="p-2 mt-4 rounded background2 aside-activity">
        <h3 class="title-style titlePerfil pl-2 pt-1">Actividad amigos</h3>
        <p v-if="actividadTotal.length === 0" class="pl-2">Comienza a seguir a un amigo para ver su actividad.</p>
        <div v-else v-for="actividad in actividadTotal" :key="actividad.id" class="p-2 mt-2 mb-2">
            <div v-if="actividad.tipoAudiovisual_id" class="d-flex justify-content-between">
                <div class="d-flex flex-column">
                    <div class="d-flex flex-row">
                        <router-link :to="{ name: 'perfil', params: { idPersona: actividad.usuario_id }}">
                            <img class="roundedPerfil m-2" v-bind:src="actividad.foto" v-bind:alt="actividad.usuario_nombre" width="45" height="45">
                        </router-link>
                        <div class="m-2">
                            <router-link :to="{ name: 'perfil', params: { idPersona: actividad.usuario_id }}">
                                {{ actividad.usuario_nombre }}
                            </router-link>
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
                <router-link :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual }}">
                    <img class="rounded p-2" v-bind:src="actividad.audiovisual_cartel" v-bind:alt="actividad.titulo_audiovisual" width="55" height="75">
                </router-link>
            </div>
            <div v-else-if="actividad.numero_capitulo">
                <span class="d-flex justify-content-between">
                    <div class="d-flex flex-column">
                        <div class="d-flex flex-row">
                            <router-link :to="{ name: 'perfil', params: { idPersona: actividad.usuario_id }}">
                                <img class="roundedPerfil m-2" v-bind:src="actividad.foto" v-bind:alt="actividad.usuario_nombre" width="45" height="45">
                            </router-link>
                            <div class="m-2">
                                <router-link :to="{ name: 'perfil', params: { idPersona: actividad.usuario_id }}">
                                    {{ actividad.usuario_nombre }}
                                </router-link>
                                ha visto el capítulo 
                                <router-link :to="{ name: 'capitulo', params: { idAudiovisual: actividad.id_audiovisual_capitulo, idCapitulo: actividad.id_capitulo }}">
                                    {{ actividad.numero_temporada }}x{{ actividad.numero_capitulo }} - {{ actividad.nombre }}
                                </router-link> de 
                                <router-link :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual_capitulo }}">
                                    {{ actividad.titulo_audiovisual_capitulo }}
                                </router-link>
                            </div>
                        </div>
                        <span class="p-letra">{{ moment(actividad.created_at).format('LL') }}</span>
                    </div>
                    <router-link :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual_capitulo }}">
                        <img class="rounded p-2" v-bind:src="actividad.capitulo_cartel" v-bind:alt="actividad.titulo_audiovisual_capitulo" width="55" height="75">
                    </router-link>
                </span>
            </div>
            <div v-else-if="actividad.numero_temporada_actividad">
                <span class="d-flex justify-content-between">
                    <div class="d-flex flex-column">
                        <div class="d-flex flex-row">
                            <router-link :to="{ name: 'perfil', params: { idPersona: actividad.usuario_id }}">
                                <img class="roundedPerfil m-2" v-bind:src="actividad.foto" v-bind:alt="actividad.usuario_nombre" width="45" height="45">
                            </router-link>
                            <div class="m-2">
                                <router-link :to="{ name: 'perfil', params: { idPersona: actividad.usuario_id }}">
                                    {{ actividad.usuario_nombre }}
                                </router-link>
                                ha visto la temporada {{ actividad.numero_temporada_actividad }} de 
                                <router-link :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual_temporada }}">
                                    {{ actividad.titulo_audiovisual_temporada }}
                                </router-link>
                            </div>
                        </div>
                        <span class="p-letra">{{ moment(actividad.created_at).format('LL') }}</span>
                    </div>
                    <router-link :to="{ name: 'audiovisual', params: { id: actividad.id_audiovisual_temporada }}">
                        <img class="rounded p-2" v-bind:src="actividad.temporada_cartel" v-bind:alt="actividad.titulo_audiovisual_temporada" width="55" height="75">
                    </router-link>
                </span>
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
            loading: true,
            moment: moment,
        }
    },
    created() {
        moment.locale('es');

        axios.get('/api/actividad-amigos')
            .then(response => this.actividadTotal = response.data)
            .catch(error => { console.log(error.response) })
            .finally(() => this.loading = false);
    },
    props: {
        cambio: {
            required: true,
            type: Number
        }
    },
    watch: {
        cambio: function () {
            axios.get('/api/actividad-amigos')
            .then(response => this.actividadTotal = response.data)
            .catch(error => { console.log(error.response) });
        }
    }
}
</script>