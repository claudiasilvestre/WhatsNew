<template>
    <v-app>
        <div>
            <app-header />

            <div class="d-flex justify-content-center">
                <b-button class="m-1" @click="loginShow = !loginShow">Iniciar sesión</b-button>
                <b-button class="m-1" @click="registroShow = !registroShow">Registrarse</b-button>
            </div>

            <b-modal 
                v-model="loginShow"
                centered
                :header-bg-variant="'dark'"
                :body-bg-variant="'dark'"
                :footer-bg-variant="'dark'"
                :hide-header="true"
                :hide-footer="true"
            >
                <login @registro="registro" @cerrarLogin="loginShow = !loginShow" />
            </b-modal>
            
            <b-modal 
                v-model="registroShow"
                centered
                :header-bg-variant="'dark'"
                :body-bg-variant="'dark'"
                :footer-bg-variant="'dark'"
                :hide-header="true"
                :hide-footer="true"
            >
                <registro @login="login" @cerrarRegistro="registroShow = !registroShow" />
            </b-modal>
        </div>
    </v-app>
</template>

<script>
import Header from './layouts/HeaderIndex.vue'
import Login from './auth/Login.vue'
import Registro from './auth/Registro.vue'

export default {
    components: {
        'app-header': Header,
        Login,
        Registro,
    },
    data() {
        return {
            loginShow: false,
            registroShow: false,
        }
    },
    methods: {
        registro() {
            this.loginShow = !this.loginShow
            this.registroShow = !this.registroShow
        },
        login() {
            this.registroShow = !this.registroShow
            this.loginShow = !this.loginShow
        }
    }
}
</script>