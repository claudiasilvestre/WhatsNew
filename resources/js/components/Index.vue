<template>
    <v-app>
        <div class="height width">
            <app-header />

            <div class="content d-flex flex-column align-items-center">
                <h1 class="content-inicio title-style">Encuentra películas y series para ver online en distintas plataformas y haz un seguimiento de ellas.</h1>
                <button class="btn btn-outline-info btn-lg m-1" @click="inicioSesionShow = !inicioSesionShow">Entra</button>
                <p class="m-1">¿No tienes una cuenta? <a class="color-registro" @click="registroShow = !registroShow">Regístrate</a></p>
                <img class="content-inicio" src="img/bg.jpg" alt="Plataformas">
            </div>

            <b-modal 
                v-model="inicioSesionShow"
                centered
                :body-bg-variant="'dark'"
                :hide-header="true"
                :hide-footer="true"
            >
                <inicioSesion @registro="registro" @cerrarInicioSesion="inicioSesionShow = !inicioSesionShow" />
            </b-modal>
            
            <b-modal 
                v-model="registroShow"
                centered
                :body-bg-variant="'dark'"
                :hide-header="true"
                :hide-footer="true"
            >
                <registro @inicioSesion="inicioSesion" @cerrarRegistro="registroShow = !registroShow" />
            </b-modal>

            <app-footer />
        </div>
    </v-app>
</template>

<script>
import Header from './layouts/HeaderIndex.vue'
import Footer from './layouts/Footer.vue'
import InicioSesion from './auth/InicioSesion.vue'
import Registro from './auth/Registro.vue'

export default {
    components: {
        'app-header': Header,
        'app-footer': Footer,
        InicioSesion,
        Registro,
    },
    data() {
        return {
            inicioSesionShow: false,
            registroShow: false,
        }
    },
    methods: {
        registro() {
            this.inicioSesionShow = !this.inicioSesionShow
            this.registroShow = !this.registroShow
        },
        inicioSesion() {
            this.registroShow = !this.registroShow
            this.inicioSesionShow = !this.inicioSesionShow
        }
    }
}
</script>