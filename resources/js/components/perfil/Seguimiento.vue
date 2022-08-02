<template>
    <div>
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 v-if="tipo === 1">Seguidos de {{ usuario.nombre }}</h5>
            <h5 v-else-if="tipo === 2">Seguidores de {{ usuario.nombre }}</h5>
            <b-icon v-if="tipo === 1" icon="x-lg" @click="cerrarSiguiendo" class="pointer"></b-icon>
            <b-icon v-else-if="tipo === 2" icon="x-lg" @click="cerrarSeguidores" class="pointer"></b-icon>
        </div>
        <div class="card-body">
            <li v-for="(seguimiento, index) in totalSeguimiento" :key="seguimiento.id" class="d-flex justify-content-between">
                <div>
                    <img class="roundedPerfil m-2" v-bind:src="seguimiento.foto" v-bind:alt="seguimiento.nombre" width="45" height="45">
                    <span>{{ seguimiento.nombre }}</span>
                </div>
                <button v-if="seguimiento.id !== currentUser.id" v-bind:class="{'btn btn-info': !clicked[index], 'btn btn-outline-info': clicked[index]}" @click="seguimientoUsuario" class="m-1">
                    {{ btnSeguimiento[index] }}
                </button>
            </li>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            btnSeguimiento: [],
            clicked: [],
            totalSeguimiento: [],
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
        if (this.tipo === 1) {
            axios.get('/api/siguiendo/'+this.usuario.id)
                .then(response => {
                    if (response.data) {
                        this.totalSeguimiento = response.data['siguiendo'];
                        this.clicked = response.data['clicked'];
                        this.btnSeguimiento = response.data['btnSeguimiento'];
                    }
                })
                .catch(error => console.log(error.response));

        } else if (this.tipo === 2) {
            axios.get('/api/seguidores/'+this.usuario.id)
                .then(response => {
                    if (response.data) {
                        this.totalSeguimiento = response.data['seguidores'];
                        this.clicked = response.data['clicked'];
                        this.btnSeguimiento = response.data['btnSeguimiento'];
                    }
                })
                .catch(error => console.log(error.response));
        }
    },
    props: {
        usuario: {
            required: true,
            type: Object
        },
        tipo: {
            required: true,
            type: Number
        }
    },
    methods: {
        seguimientoUsuario() {
            axios.post('/api/seguimiento-usuario/', 
            { 
                usuarioActual_id: this.currentUser.id, 
                usuario_id: this.usuario.id, 
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
        },
        cerrarSiguiendo() {
            this.$emit('cerrarSiguiendo');
        },
        cerrarSeguidores() {
            this.$emit('cerrarSeguidores');
        }
    }
}
</script>