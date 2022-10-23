<template>
    <div class="list">
        <div>
            <div class="d-flex flex-row">
                <img class="roundedPerfil m-3" v-bind:src="usuarioActual.foto" v-bind:alt="usuarioActual.nombre" width="50" height="50">
                <p class="width">
                    <textarea v-if="audiovisual" v-model="formData.texto" v-bind:placeholder="'¿Qué te ha parecido '+audiovisual.titulo+'?'"></textarea>
                    <textarea v-else v-model="formData.texto" placeholder="¿Qué te ha parecido este capítulo?"></textarea>
                </p>
            </div>
            <div class="d-flex justify-content-end">
                <button @click="guardarComentario" class="btn btn-info mt-3">Comentar</button>
            </div>
        </div>
        <b-tabs class="mt-8">
            <b-tab title="Más recientes" class="tabsComentarios" active>
                <lista-comentarios v-if="audiovisual" :audiovisual="audiovisual" :creado="clicked" :tipo=1 @cambio="cambio" />
                <lista-comentarios v-else :capitulo="capitulo" :creado="clicked" :tipo=1 @cambio="cambio" />
            </b-tab>
            <b-tab title="Mejor valorados" class="tabsComentarios">
                <lista-comentarios v-if="audiovisual" :audiovisual="audiovisual" :creado="clicked" :tipo=2 @cambio="cambio" />
                <lista-comentarios v-else :capitulo="capitulo" :creado="clicked" :tipo=2 @cambio="cambio" />
            </b-tab>
        </b-tabs>
    </div>
</template>

<script>
import ListaComentarios from './ListaComentarios.vue'

export default {
    components: {
        ListaComentarios
    },
    props: {
        audiovisual: {
            type: Object
        },
        capitulo: {
            type: Object
        }
    },
    data() {
        return {
            formData: {
                tipo_id: '',
                usuario_id: '',
                texto: '',
            },
            clicked: false,
        }
    },
    computed: {
        usuarioActual: {
            get() {
                return this.$store.state.usuarioActual.usuario;
            }
        }
    },
    methods: {
        guardarComentario() {
            if (this.formData.texto) {
                this.formData.usuario_id = this.usuarioActual.id;
                if (this.audiovisual) {
                    this.formData.tipo_id = this.audiovisual.id;
                    axios.post('/api/guardar-comentario-audiovisual', this.formData).then(() => {
                        this.formData.texto = this.formData.tipo_id =''
                    }).catch((errors) => {
                        console.log(errors.response)
                    });
                } else {
                    this.formData.tipo_id = this.capitulo.id;
                    axios.post('/api/guardar-comentario-capitulo', this.formData).then(() => {
                        this.formData.texto = this.formData.tipo_id = ''
                    }).catch((errors) => {
                        console.log(errors.response)
                    });
                }
                this.clicked = !this.clicked;
            }
        },
        cambio() {
            this.clicked = !this.clicked;
        }
    }
}
</script>