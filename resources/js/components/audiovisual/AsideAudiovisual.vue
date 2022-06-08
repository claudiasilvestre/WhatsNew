<template>
    <div>
        <img class="rounded" v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" width="200" height="300">
        <div v-if="!loading" class="buttons width">
            <button v-bind:class="{'btn btn-danger': !clicked1, 'btn btn-outline-danger': clicked1}" @click="seguimiento(1)" class="m-1"><b-icon icon="clock"></b-icon>
                {{ pendiente }}
            </button>
            <button v-bind:class="{'btn btn-warning': !clicked2, 'btn btn-outline-warning': clicked2}" @click="seguimiento(2)" class="m-1"><b-icon icon="eye"></b-icon>
                {{ seguir }}
            </button>
            <button v-bind:class="{'btn btn-info': !clicked3, 'btn btn-outline-info': clicked3}" @click="seguimiento(3)" class="m-1"><b-icon icon="check2"></b-icon>
                {{ vista }}
            </button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            pendiente: "Pendiente",
            seguir: "Seguir",
            vista: "Vista",
            loading: true,
            clicked1: false,
            clicked2: false,
            clicked3: false,
        }
    },
    props: {
      audiovisual: {
        required: true,
        type: Object
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
        axios.get('/api/saber-seguimiento/', {
            params: { 
                audiovisual_id: this.audiovisual.id, 
                usuario_id: 1,
            }})
            .then(response => {
                if (response.data === 1) {
                    // this.pendiente = "Pendiente2";
                    this.clicked1 = true;
                } else if (response.data === 2) {
                    // this.seguir = "Seguir2";
                    this.clicked2 = true;
                } else if (response.data === 3) {
                    // this.vista = "Vista2";
                    this.clicked3 = true;
                }
            })
            .catch(error => console.log(error.response) )
            .finally(() => this.loading = false);
    },
    methods: {
        seguimiento(tipo) {
            axios.post('/api/seguimiento/', 
            { 
                audiovisual_id: this.audiovisual.id, 
                usuario_id: 1, 
                tipo 
            })
            .then(response => {
                if (tipo === 1) {
                    if (response.data) {
                        /* this.pendiente = "Pendiente2";
                        this.seguir = "Seguir";
                        this.vista = "Vista"; */
                        this.clicked1 = true;
                        this.clicked2 = false;
                        this.clicked3 = false;
                    } else
                        // this.pendiente = "Pendiente";
                        this.clicked1 = false;
                } else if (tipo === 2) {
                    if (response.data) {
                        /* this.pendiente = "Pendiente";
                        this.seguir = "Seguir2";
                        this.vista = "Vista"; */
                        this.clicked1 = false;
                        this.clicked2 = true;
                        this.clicked3 = false;
                    } else
                        // this.seguir = "Seguir";
                        this.clicked2 = false;
                } else if (tipo === 3) {
                    if (response.data) {
                        /* this.pendiente = "Pendiente";
                        this.seguir = "Seguir";
                        this.vista = "Vista2"; */
                        this.clicked1 = false;
                        this.clicked2 = false;
                        this.clicked3 = true;
                    } else
                        // this.vista = "Vista";
                        this.clicked3 = false;
                }

                this.clicked = true;
            })
            .catch(error => console.log(error.response) );
        },
    }
}
</script>