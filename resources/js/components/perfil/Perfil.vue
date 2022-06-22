<template>
    <v-app>
        <div>
            <app-header />
            <div class="list">
                <h2>{{ currentUser.nombre }}</h2>
                <b-tabs>
                    <b-tab title="Actividad" active><actividad /></b-tab>
                    <b-tab title="Colección"></b-tab>
                </b-tabs>
            </div>
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import Actividad from '../Actividad.vue'

export default {
    components: {
        'app-header': Header,
        Actividad,
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