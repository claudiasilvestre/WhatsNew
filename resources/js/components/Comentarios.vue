<template>
    <div class="list">
        <div>
            <textarea v-if="audiovisual" v-model="formData.texto" v-bind:placeholder="'¿Qué te ha parecido '+audiovisual.titulo+'?'"></textarea>
            <textarea v-else v-model="formData.texto" placeholder="¿Qué te ha parecido este capítulo?"></textarea>
            <p class="text-danger" v-text="errors.texto"></p>
            <div class="d-flex justify-content-end">
                <b-button @click="guardarComentario" variant="info" class="mt-3">Comentar</b-button>
            </div>
        </div>
        <b-tabs>
            <b-tab title="Más recientes" class="tabsComentarios" active>
                <lista-comentarios v-if="audiovisual" :audiovisual="audiovisual" :clicked="clicked"/>
                <lista-comentarios v-else :capitulo="capitulo" :clicked="clicked"/>
            </b-tab>
            <b-tab title="Mejor valorados" class="tabsComentarios">Todavía no hay comentarios</b-tab>
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
                usuario_id: 1,
                texto: '',
            },
            errors: {},
            clicked: false,
        }
    },
    methods: {
        guardarComentario() {
            if (this.audiovisual) {
                this.formData.tipo_id = this.audiovisual.id;
                axios.post('/api/guardar-comentario-audiovisual', this.formData).then((response) => {
                    console.log(response.data)
                    this.formData.texto = this.formData.tipo_id =''
                }).catch((errors) => {
                    this.errors = errors.response.data.errors
                });
            } else {
                this.formData.tipo_id = this.capitulo.id;
                axios.post('/api/guardar-comentario-capitulo', this.formData).then((response) => {
                    console.log(response.data)
                    this.formData.texto = this.formData.tipo_id = ''
                }).catch((errors) => {
                    this.errors = errors.response.data.errors
                });
            }
            this.clicked = !this.clicked;
        }
    }
}
</script>