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
                <h2>{{ usuario.nombre }}</h2>
                <span>{{ usuario.seguidos }} Siguiendo</span>
                <span>{{ usuario.seguidores }} Seguidores</span>
                <router-link v-if="Number(usuario_id) === currentUser.id" :to="{ name: 'ajustes', params: { idPersona: currentUser.id }}">
                    <button class="btn btn-info m-1"><b-icon icon="tools"></b-icon>
                        Editar perfil
                    </button>
                </router-link>
                <button v-else v-bind:class="{'btn btn-info': !clicked, 'btn btn-outline-info': clicked}" @click="seguimientoUsuario" class="m-1">
                    {{ seguimiento }}
                </button>
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
    data() {
        return {
            usuario_id: this.$route.params.idPersona,
            seguimiento: "Seguir",
            clicked: false,
            usuario: {},
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
            .catch(error => { console.log(error.response) });

        this.$watch(
            () => this.$route.params,
            (toParams, previousParams) => {
                this.$forceUpdate();
            }
        )
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
        }
    }
}
</script>