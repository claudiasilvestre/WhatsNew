<template>
    <div>
        <h3>Actividad</h3>
        <div v-for="actividad in actividadTotal" :key="actividad.id" class="p-1 mb-4 rounded background2">
            <div v-if="actividad.tipoAudiovisual_id">
                <img class="roundedPerfil" v-bind:src="actividad.foto" v-bind:alt="actividad.usuario_nombre" width="45" height="45">
                {{ actividad.usuario_nombre }}
                <span v-if="actividad.tipo === 1"> ha marcado como pendiente </span>
                <span v-else-if="actividad.tipo === 2"> sigue </span>
                <span v-else-if="actividad.tipo === 3"> ha visto </span>
                <span v-if="actividad.tipoAudiovisual_id === 1">la película </span>
                <span v-else-if="actividad.tipoAudiovisual_id === 2">la serie </span>
                <span>{{ actividad.titulo_audiovisual }}</span>
            </div>
            <div v-else-if="actividad.numero_capitulo">
                <span>
                    <img class="roundedPerfil" v-bind:src="actividad.foto" v-bind:alt="actividad.usuario_nombre" width="45" height="45">
                    {{ actividad.usuario_nombre }} ha visto el capítulo {{ actividad.numero_temporada }}x{{ actividad.numero_capitulo }} 
                    - {{ actividad.nombre }} de {{ actividad.titulo_audiovisual_capitulo }}
                </span>
            </div>
            <div v-else-if="actividad.numero_temporada_actividad">
                <span>
                    <img class="roundedPerfil" v-bind:src="actividad.foto" v-bind:alt="actividad.usuario_nombre" width="45" height="45">
                    {{ actividad.usuario_nombre }} ha visto la temporada {{ actividad.numero_temporada_actividad }} 
                    de {{ actividad.titulo_audiovisual_temporada }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            actividadTotal: [],
        }
    },
    created() {
        axios.get('/api/actividad_amigos')
            .then(response => this.actividadTotal = response.data)
            .catch(error => { console.log(error.response) });
    }
}
</script>