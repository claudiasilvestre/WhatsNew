<template>
    <div>
        <div v-for="actividad in actividadTotal" :key="actividad.id" class="p-1 mb-4 rounded background2">
            <div v-if="actividad.tipoAudiovisual_id">
                {{ currentUser.nombre }}
                <span v-if="actividad.tipo === 1"> ha marcado como pendiente </span>
                <span v-else-if="actividad.tipo === 2"> sigue </span>
                <span v-else-if="actividad.tipo === 3"> ha visto </span>
                <span v-if="actividad.tipoAudiovisual_id === 1">la película </span>
                <span v-else-if="actividad.tipoAudiovisual_id === 2">la serie </span>
                <span>{{ actividad.titulo_audiovisual }}</span>
            </div>
            <div v-else-if="actividad.numero_capitulo">
                <span>
                    {{ currentUser.nombre }} ha visto el capítulo {{ actividad.numero_temporada }}x{{ actividad.numero_capitulo }} 
                    - {{ actividad.nombre }} de {{ actividad.titulo_audiovisual_capitulo }}
                </span>
            </div>
            <div v-else-if="actividad.numero_temporada_actividad">
                <span>
                    {{ currentUser.nombre }} ha visto la temporada {{ actividad.numero_temporada_actividad }} 
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
    computed: {
        currentUser: {
            get() {
                return this.$store.state.currentUser.user;
            }
        }
    },
    created() {
        axios.get('/api/actividad-usuario/'+this.currentUser.id)
            .then(response => {
                this.actividadTotal = response.data;
            })
            .catch(error => { console.log(error.response) });
    }
}
</script>