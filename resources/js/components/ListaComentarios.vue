<template>
    <div class="list">
        <div v-for="(comentario, index) in comentarios" :key="comentario.id" class="p-1 mb-4 rounded background2">
            <span>{{ comentario.nombre }}</span>
            <p>{{ comentario.texto }}</p>
            <div class="d-flex justify-content-end">
                <button @click="votoPositivo(comentario.id)" class="m-1">
                    <b-icon v-if="!clickedLike[index]" icon="hand-thumbs-up"></b-icon>
                    <b-icon v-else icon="hand-thumbs-up-fill"></b-icon>
                </button>
                <button @click="votoNegativo(comentario.id)" class="m-1">
                    <b-icon v-if="!clickedDislike[index]" icon="hand-thumbs-down"></b-icon>
                    <b-icon v-else icon="hand-thumbs-down-fill"></b-icon>
                </button>
                <button v-if="comentario.persona_id === usuario_id" class="btn btn-danger m-1" @click="borrarComentario(comentario.id)">Borrar</button>
            </div>
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
            usuario_id: 1,
            formData: {
                usuario_id: 1,
                comentario_id: ''
            },
            clickedLike: [],
            clickedDislike: [],
        }
    },
    created() {
        if (this.audiovisual) {
            axios.get('/api/comentario-audiovisual/'+this.audiovisual.id)
                .then(response => this.comentarios = response.data)
                .catch(error => { console.log(error.response) })
            .finally(() => {
                axios.get('/api/clicked-audiovisual', {
                    params: {
                        comentarios: this.comentarios
                    }})
                    .then(response => {
                        this.clickedLike = response.data['clickedLike'];
                        this.clickedDislike = response.data['clickedDislike'];
                    })
                    .catch(error => { console.log(error.response) });
            });
        } else {
            axios.get('/api/comentario-capitulo/'+this.capitulo.id)
                .then(response => this.comentarios = response.data)
                .catch(error => { console.log(error.response) })
                .finally(() => {
                    axios.get('/api/clicked-capitulo', {
                        params: {
                            comentarios: this.comentarios
                        }})
                        .then(response => {
                            this.clickedLike = response.data['clickedLike'];
                            this.clickedDislike = response.data['clickedDislike'];
                        })
                        .catch(error => { console.log(error.response) });
                });
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
    },
    methods: {
        borrarComentario(comentario_id) {
            if (this.audiovisual) {
                axios.post('/api/borrar-comentario-audiovisual/'+comentario_id).then((response) => {
                    console.log(response.data)
                }).catch(error => console.log(error.response));

                axios.get('/api/comentario-audiovisual/'+this.audiovisual.id)
                    .then(response => this.comentarios = response.data)
                    .catch(error => { console.log(error.response) });
            } else {
                axios.post('/api/borrar-comentario-capitulo/'+comentario_id).then((response) => {
                    console.log(response.data)
                }).catch(error => console.log(error.response));

                axios.get('/api/comentario-capitulo/'+this.capitulo.id)
                    .then(response => this.comentarios = response.data)
                    .catch(error => { console.log(error.response) });
            }
        },
        votoPositivo(comentario_id) {
            this.formData.comentario_id = comentario_id;
            if (this.audiovisual) {
                axios.post('/api/opinion-positiva-audiovisual', this.formData).then((response) => {
                    console.log(response.data)
                    this.formData.comentario_id = ''
                }).catch(error => console.log(error.response))
                    .finally(() => {
                        axios.get('/api/clicked-audiovisual', {
                            params: {
                                comentarios: this.comentarios
                            }})
                            .then(response => {
                                this.clickedLike = response.data['clickedLike'];
                                this.clickedDislike = response.data['clickedDislike'];
                            })
                            .catch(error => { console.log(error.response) });
                    });
            } else {
                axios.post('/api/opinion-positiva-capitulo', this.formData).then((response) => {
                    console.log(response.data)
                    this.formData.comentario_id = ''
                }).catch(error => console.log(error.response))
                    .finally(() => {
                        axios.get('/api/clicked-capitulo', {
                            params: {
                                comentarios: this.comentarios
                            }})
                            .then(response => {
                                this.clickedLike = response.data['clickedLike'];
                                this.clickedDislike = response.data['clickedDislike'];
                            })
                            .catch(error => { console.log(error.response) });
                    });
            }
        },
        votoNegativo(comentario_id) {
            this.formData.comentario_id = comentario_id;
            if (this.audiovisual) {
                axios.post('/api/opinion-negativa-audiovisual', this.formData).then((response) => {
                    console.log(response.data)
                    this.formData.comentario_id = ''
                }).catch(error => console.log(error.response))
                    .finally(() => {
                        axios.get('/api/clicked-audiovisual', {
                            params: {
                                comentarios: this.comentarios
                            }})
                            .then(response => {
                                this.clickedLike = response.data['clickedLike'];
                                this.clickedDislike = response.data['clickedDislike'];
                            })
                            .catch(error => { console.log(error.response) });
                    });
            } else {
                axios.post('/api/opinion-negativa-capitulo', this.formData).then((response) => {
                    console.log(response.data)
                    this.formData.comentario_id = ''
                }).catch(error => console.log(error.response))
                    .finally(() => {
                        axios.get('/api/clicked-capitulo', {
                            params: {
                                comentarios: this.comentarios
                            }})
                            .then(response => {
                                this.clickedLike = response.data['clickedLike'];
                                this.clickedDislike = response.data['clickedDislike'];
                            })
                            .catch(error => { console.log(error.response) });
                    });
            }
        }
    }
}
</script>