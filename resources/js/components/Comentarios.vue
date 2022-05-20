<template>
    <div class="list">
        <form>
            <textarea v-if="audiovisual" v-bind:placeholder="'¿Qué te ha parecido '+audiovisual.titulo+'?'"></textarea>
            <textarea v-else v-bind:placeholder="'¿Qué te ha parecido este capítulo?'"></textarea>
            <div class="d-flex justify-content-end">
                <b-button variant="info" class="mt-3">Comentar</b-button>
            </div>
        </form>
        <b-tabs>
            <b-tab title="Mejor valorados" class="tabsComentarios" active>Todavía no hay comentarios</b-tab>
            <b-tab title="Más recientes" class="tabsComentarios" >Todavía no hay comentarios</b-tab>
        </b-tabs>
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
        }
    }
}
</script>