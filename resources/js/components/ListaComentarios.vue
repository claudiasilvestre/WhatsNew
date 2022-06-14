<template>
    <div class="list">
        <div v-for="comentario in comentarios" :key="comentario.id" class="p-1 mb-4 rounded background2">
            <span>{{ comentario.nombre }}</span>
            <p>{{ comentario.texto }}</p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        audiovisual: {
            type: Object
        },
        capitulo: {
            type: Object
        },
        clicked: {
            required: true,
            type: Boolean
        }
    },
    data() {
        return {
            comentarios: [],
        }
    },
    created() {
        if (this.audiovisual) {
            axios.get('/api/comentario-audiovisual/'+this.audiovisual.id)
            .then(response => this.comentarios = response.data)
            .catch(error => { console.log(error.response) });
        } else {
            axios.get('/api/comentario-capitulo/'+this.capitulo.id)
            .then(response => this.comentarios = response.data)
            .catch(error => { console.log(error.response) });
        }
    },
    watch: {
        clicked: function () {
            if (this.audiovisual) {
                axios.get('/api/comentario-audiovisual/'+this.audiovisual.id)
                .then(response => this.comentarios = response.data)
                .catch(error => { console.log(error.response) });
            } else {
                axios.get('/api/comentario-capitulo/'+this.capitulo.id)
                .then(response => this.comentarios = response.data)
                .catch(error => { console.log(error.response) });
            }
        }
    }
}
</script>