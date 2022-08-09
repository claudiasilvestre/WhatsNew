<template>
    <v-app>
        <div class="height width">
            <app-header />

            <div class="content d-flex flex-column align-items-center">
                <h1 class="content-inicio home-title">Encuentra películas y series para ver online en distintas plataformas y haz un seguimiento de ellas.</h1>
                <button class="btn btn-outline-info btn-lg m-1" @click="loginShow = !loginShow">Entra</button>
                <p class="m-1">¿No tienes una cuenta? <a class="color-registro" @click="registroShow = !registroShow">Regístrate</a></p>
                <img class="content-inicio" src="img/bg.jpg" alt="Plataformas">
            </div>

            <b-modal 
                v-model="loginShow"
                centered
                :body-bg-variant="'dark'"
                :hide-header="true"
                :hide-footer="true"
            >
                <login @registro="registro" @cerrarLogin="loginShow = !loginShow" />
            </b-modal>
            
            <b-modal 
                v-model="registroShow"
                centered
                :body-bg-variant="'dark'"
                :hide-header="true"
                :hide-footer="true"
            >
                <registro @login="login" @cerrarRegistro="registroShow = !registroShow" />
            </b-modal>

            <app-footer />
        </div>
    </v-app>
</template>

<script>
import Header from './layouts/HeaderIndex.vue'
import Footer from './layouts/Footer.vue'
import Login from './auth/Login.vue'
import Registro from './auth/Registro.vue'

export default {
    components: {
        'app-header': Header,
        'app-footer': Footer,
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