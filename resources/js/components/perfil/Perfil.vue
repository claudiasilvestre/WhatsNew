<template>
    <v-app>
        <div>
            <app-header />
            <div v-if="Object.keys(currentUser).length === 0" class="content d-flex justify-content-center flex-column align-items-center" style="height:40vh;">
                <b-spinner
                    :variant="'light'"
                    :key="'light'"
                ></b-spinner>
            </div>
            <div v-else class="list content d-flex flex-column">
                <img class="roundedPerfil" v-bind:src="usuario.foto" v-bind:alt="usuario.nombre" width="100" height="100">
                <div class="d-flex flex-column mb-3">
                    <span class="titlePerfil">{{ usuario.nombre }}</span>
                    <span>{{ usuario.usuario }}</span>
                </div>
                <span>{{ usuario.puntos }} puntos</span>
                <div>
                    <a @click="siguiendoShow = !siguiendoShow">{{ usuario.seguidos }} Siguiendo</a>
                    <a @click="seguidoresShow = !seguidoresShow">{{ usuario.seguidores }} Seguidores</a>
                    <router-link v-if="Number(usuario_id) === currentUser.id" :to="{ name: 'ajustes', params: { idPersona: currentUser.id }}">
                        <button class="btn btn-info m-1"><b-icon icon="tools"></b-icon>
                            Editar perfil
                        </button>
                    </router-link>
                    <button v-else v-bind:class="{'btn btn-info': !clicked, 'btn btn-outline-info': clicked}" @click="seguimientoUsuario" class="m-1">
                        {{ seguimiento }}
                    </button>
                </div>
                <div class="row-between">
                    <b-tabs>
                        <b-tab title="Actividad" active><actividad @cambio="cambio" /></b-tab>
                        <b-tab title="Colección"><coleccion /></b-tab>
                    </b-tabs>
                    <actividad-aside v-if="Number(usuario_id) === currentUser.id" :cambio="componentKey" />
                </div>
            </div>

            <b-modal 
                v-model="siguiendoShow"
                centered
                :body-bg-variant="'dark'"
                :hide-header="true"
                :hide-footer="true"
            >
                <seguimiento :usuario="usuario" :tipo="1" @cerrarSiguiendo="siguiendoShow = !siguiendoShow" />
            </b-modal>
            
            <b-modal 
                v-model="seguidoresShow"
                centered
                :body-bg-variant="'dark'"
                :hide-header="true"
                :hide-footer="true"
            >
                <seguimiento :usuario="usuario" :tipo="2" @cerrarSeguidores="seguidoresShow = !seguidoresShow" />
            </b-modal>

            <app-footer />
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import Footer from '../layouts/Footer.vue'
import Actividad from './ActividadPerfil.vue'
import Coleccion from './Coleccion.vue'
import ActividadAside from './ActividadAside.vue'
import Seguimiento from './Seguimiento.vue'

export default {
    components: {
        'app-header': Header,
        'app-footer': Footer,
        Actividad,
        Coleccion,
        ActividadAside,
        Seguimiento,
    },
    data() {
        return {
            usuario_id: this.$route.params.idPersona,
            seguimiento: "Seguir",
            clicked: false,
            usuario: {},
            componentKey: 0,
            siguiendoShow: false,
            seguidoresShow: false,
        }
    },
    computed: {
        currentUser: {
            get() {
                return this.$store.state.currentUser.user;
            }
        }
    },
    created() {
        axios.get('/api/personas/'+this.usuario_id)
            .then(response => this.usuario = response.data[0])
            .catch(error => { console.log(error.response) })
            .finally(() => document.title = this.usuario.nombre + " - What's new");
    },
    beforeUpdate() {
        axios.get('/api/saber-seguimiento-usuario/', {
            params: { 
                usuarioActual_id: this.currentUser.id, 
                usuario_id: this.usuario_id,
            }})
            .then(response => {
                if (response.data) {
                    this.seguimiento = "Siguiendo";
                    this.clicked = true;
                }
            })
            .catch(error => console.log(error.response));
    },
    methods: {
        seguimientoUsuario() {
            axios.post('/api/seguimiento-usuario/', 
            { 
                usuarioActual_id: this.currentUser.id, 
                usuario_id: this.usuario_id, 
            })
            .then(response => {
                if (response.data) {
                    this.seguimiento = "Siguiendo";
                    this.clicked = true;
                } else {
                    this.seguimiento = "Seguir";
                    this.clicked = false;
                }
            })
            .catch(error => console.log(error.response));

            axios.get('/api/personas/'+this.usuario_id)
                .then(response => this.usuario = response.data[0])
                .catch(error => { console.log(error.response) });
        },
        cambio() {
            this.componentKey += 1;
        }
    }
}
</script>