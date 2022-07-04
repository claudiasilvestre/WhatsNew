<template>
    <v-app>
        <div>
            <app-header />
            <b-tabs>
                <b-tab title="Series" active><menu-coleccion :tipo="2"/></b-tab>
                <b-tab title="Peliculas"><menu-coleccion :tipo="1"/></b-tab>
            </b-tabs>
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import MenuColeccion from './MenuColeccion.vue'

export default {
    components: {
        'app-header': Header,
        MenuColeccion,
    },
}
</script>