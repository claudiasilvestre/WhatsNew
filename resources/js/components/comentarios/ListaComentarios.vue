<template>
    <div class="list">
        <p v-if="comentarios.length === 0" >Todavía no hay comentarios</p>
        <div v-else v-for="(comentario, index) in comentarios" :key="comentario.id" class="p-1 mb-4 rounded background2 d-flex flex-row">
            <img class="roundedPerfil m-3" v-bind:src="comentario.foto" v-bind:alt="comentario.nombre" width="50" height="50">
            <div class="width">
                <div d-flex flex-row>
                    <span class="secundary-color">{{ comentario.nombre }}</span>
                    <span class="p-letra">{{ moment(comentario.created_at).format('LL') }}</span>
                </div>
                <span>{{ comentario.texto }}</span>
                <div class="d-flex justify-content-end align-items-center">
                    <button @click="votoPositivo(comentario.id)" class="m-1">
                        <b-icon v-if="!clickedLike[index]" icon="hand-thumbs-up"></b-icon>
                        <b-icon v-else icon="hand-thumbs-up-fill"></b-icon>
                        <span>{{ comentario.votosPositivos }}</span>
                    </button>
                    <button @click="votoNegativo(comentario.id)" class="m-1">
                        <b-icon v-if="!clickedDislike[index]" icon="hand-thumbs-down"></b-icon>
                        <b-icon v-else icon="hand-thumbs-down-fill"></b-icon>
                        <span>{{ comentario.votosNegativos }}</span>
                    </button>
                    <b-icon v-if="comentario.persona_id === usuarioActual.id" icon="trash" variant="danger" class="pointer m-2" @click="borrarComentario(comentario.id)"></b-icon>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";

export default {
    props: {
        audiovisual: {
            type: Object
        },
        capitulo: {
            type: Object
        },
        creado: {
            required: true,
            type: Boolean
        },
        tipo: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            comentarios: [],
            formData: {
                usuario_id: '',
                comentario_id: ''
            },
            clickedLike: [],
            clickedDislike: [],
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

        if (this.audiovisual) {
            this.comentariosAudiovisual();
        } else {
            this.comentariosCapitulo();
        }
    },
    watch: {
        creado: function () {
            if (this.audiovisual) {
                this.comentariosAudiovisual();

            } else {
                this.comentariosCapitulo();
            }
        }
    },
    methods: {
        comentariosAudiovisual() {
            axios.get('/api/comentarios-audiovisual/', {
            params: { 
                audiovisual_id: this.audiovisual.id, 
                tipo: this.tipo,
            }})
            .then(response => {
                this.comentarios = response.data['comentarios'];
                this.clickedLike = response.data['clickedLike'];
                this.clickedDislike = response.data['clickedDislike'];
            })
            .catch(error => console.log(error.response));
        },
        comentariosCapitulo() {
            axios.get('/api/comentarios-capitulo/', {
            params: { 
                capitulo_id: this.capitulo.id, 
                tipo: this.tipo,
            }})
            .then(response => {
                this.comentarios = response.data['comentarios'];
                this.clickedLike = response.data['clickedLike'];
                this.clickedDislike = response.data['clickedDislike'];
            })
            .catch(error => console.log(error.response));
        },
        borrarComentario(comentario_id) {
            if (this.audiovisual) {
                axios.post('/api/borrar-comentario-audiovisual/'+comentario_id)
                .catch(error => console.log(error.response));

                this.comentariosAudiovisual();
            } else {
                axios.post('/api/borrar-comentario-capitulo/'+comentario_id)
                .catch(error => console.log(error.response));

                this.comentariosCapitulo();
            }
            this.$emit('cambio');
        },
        votoPositivo(comentario_id) {
            this.formData.comentario_id = comentario_id;
            this.formData.usuario_id = this.usuarioActual.id;
            if (this.audiovisual) {
                axios.post('/api/opinion-positiva-comentario-audiovisual', this.formData)
                .then(() => {
                    this.formData.comentario_id = ''
                })
                .catch(error => console.log(error.response));

                this.comentariosAudiovisual()
            } else {
                axios.post('/api/opinion-positiva-comentario-capitulo', this.formData)
                .then(() => {
                    this.formData.comentario_id = ''
                })
                .catch(error => console.log(error.response));

                this.comentariosCapitulo()
            }

            this.$emit('cambio');
        },
        votoNegativo(comentario_id) {
            this.formData.comentario_id = comentario_id;
            this.formData.usuario_id = this.usuarioActual.id;
            if (this.audiovisual) {
                axios.post('/api/opinion-negativa-comentario-audiovisual', this.formData)
                .then(() => {
                    this.formData.comentario_id = ''
                })
                .catch(error => console.log(error.response));

                this.comentariosAudiovisual();
            } else {
                axios.post('/api/opinion-negativa-comentario-capitulo', this.formData)
                .then(() => {
                    this.formData.comentario_id = ''
                })
                .catch(error => console.log(error.response));

                this.comentariosCapitulo()
            }

            this.$emit('cambio');
        }
    }
}
</script>
