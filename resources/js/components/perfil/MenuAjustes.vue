<template>
    <v-app>
        <div>
            <app-header />
            <div class="content">
                <b-tabs vertical>
                    <b-tab title="Información personal" active><ajustes /></b-tab>
                    <b-tab title="Contraseña"><contraseña /></b-tab>
                </b-tabs>
            </div>
            <app-footer />
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import Footer from '../layouts/Footer.vue'
import Ajustes from './Ajustes.vue'
import Contraseña from './Contraseña.vue'

export default {
    components: {
        'app-header': Header,
        'app-footer': Footer,
        Ajustes,
        Contraseña,
    },
    mounted() {
        document.title = "Editar perfil - WhatsNew"
    },
}
</script>