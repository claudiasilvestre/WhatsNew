<template>
    <v-app>
        <div>
            <app-header />
            <div v-if="Object.keys(currentUser).length === 0" class="d-flex justify-content-center flex-column align-items-center" style="height:40vh;">
                <b-spinner
                    :variant="'light'"
                    :key="'light'"
                ></b-spinner>
            </div>
            <div v-else class="list">
                <h2>{{ currentUser.nombre }}</h2>
                <b-tabs>
                    <b-tab title="Actividad" active><actividad /></b-tab>
                    <b-tab title="Colección"><coleccion /></b-tab>
                </b-tabs>
            </div>
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import Actividad from '../Actividad.vue'
import Coleccion from './Coleccion.vue'

export default {
    components: {
        'app-header': Header,
        Actividad,
        Coleccion,
    },
    computed: {
        currentUser: {
            get() {
                return this.$store.state.currentUser.user;
            }
        }
    },
}
</script>