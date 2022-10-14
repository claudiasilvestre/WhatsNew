<template>
    <v-app>
        <div>
            <app-header />
            <div class="content">
                <h3 class="titleMiColeccion">Mi colección</h3>
                <b-tabs>
                    <b-tab title="Series" active><menu-coleccion :tipo="2"/></b-tab>
                    <b-tab title="Peliculas"><menu-coleccion :tipo="1"/></b-tab>
                </b-tabs>
            </div>
            <app-footer />
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import Footer from '../layouts/Footer.vue'
import MenuColeccion from './MenuColeccion.vue'

export default {
    components: {
        'app-header': Header,
        'app-footer': Footer,
        MenuColeccion,
    },
    mounted() {
        document.title = "Mi colección - WhatsNew"
    }
}
</script>
