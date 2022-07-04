<template>
    <div>
        <b-tabs>
            <b-tab title="Vistas"><lista-coleccion /></b-tab>
            <b-tab title="Pendientes"><lista-coleccion /></b-tab>
            <b-tab title="Todo"><lista-coleccion /></b-tab>
        </b-tabs>
    </div>
</template>

<script>
import ListaColeccion from './ListaColeccion.vue'

export default {
    components: {
        ListaColeccion
    },
}
</script>