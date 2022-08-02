<template>
    <v-app>
        <div class="height width">
            <app-header />

            <div class="d-flex justify-content-center align-items-center m-5">
                <button class="btn btn-outline-info btn-lg m-1" @click="loginShow = !loginShow">Entra</button>
                <p class="m-1">¿No tienes una cuenta? <a class="color-registro" @click="registroShow = !registroShow">Regístrate</a></p>
            </div>
            
            <div class="d-flex justify-content-center width" style="height:75%;">
                <div class="bg-inicio height" style="width:60%;"></div>
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