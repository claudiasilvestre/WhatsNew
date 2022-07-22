<template>
    <div>
        <div v-for="actividad in actividadTotal" :key="actividad.id" class="p-1 mb-4 rounded background2 d-flex justify-content-between">
            <div v-if="actividad.tipoAudiovisual_id">
                {{ usuario.nombre }}
                <span v-if="actividad.tipo === 1"> ha marcado como pendiente </span>
                <span v-else-if="actividad.tipo === 2"> sigue </span>
                <span v-else-if="actividad.tipo === 3"> ha visto </span>
                <span v-if="actividad.tipoAudiovisual_id === 1">la película </span>
                <span v-else-if="actividad.tipoAudiovisual_id === 2">la serie </span>
                <span>{{ actividad.titulo_audiovisual }}</span>
            </div>
            <div v-else-if="actividad.numero_capitulo">
                <span>
                    {{ usuario.nombre }} ha visto el capítulo {{ actividad.numero_temporada }}x{{ actividad.numero_capitulo }} 
                    - {{ actividad.nombre }} de {{ actividad.titulo_audiovisual_capitulo }}
                </span>
            </div>
            <div v-else-if="actividad.numero_temporada_actividad">
                <span>
                    {{ usuario.nombre }} ha visto la temporada {{ actividad.numero_temporada_actividad }} 
                    de {{ actividad.titulo_audiovisual_temporada }}
                </span>
            </div>
            <b-icon icon="x-circle" variant="danger" @click="borrarActividad(actividad.id)" class="pointer"></b-icon>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            actividadTotal: [],
            usuario_id: this.$route.params.idPersona,
        }
    },
    created() {
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
            .then((response) => {
                console.log(response.data)
            })
            .catch((errors) => {
                this.errors = errors.response.data.errors
            });

            axios.get('/api/actividad-usuario/'+this.usuario_id)
            .then(response => {
                this.actividadTotal = response.data;
            })
            .catch(error => { console.log(error.response) });
        }
    }
}
</script>