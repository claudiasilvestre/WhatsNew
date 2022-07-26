<template>
    <v-app>
        <div>
            <app-header />
            <b-tabs vertical>
                <b-tab title="Información personal" active><ajustes /></b-tab>
                <b-tab title="Contraseña"><contraseña /></b-tab>
            </b-tabs>
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import Ajustes from './Ajustes.vue'
import Contraseña from './Contraseña.vue'

export default {
    components: {
        'app-header': Header,
        Ajustes,
        Contraseña,
    },
    mounted() {
        document.title = "Editar perfil - What's new"
    },
}
</script>