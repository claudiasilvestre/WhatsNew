<template>
    <div>
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Registro</h5>
            <b-icon icon="x-lg" @click="cerrarRegistro" class="pointer"></b-icon>
        </div>
        <div class="card-body">
            <p class="form-group">
                <input type="text" class="p-2" placeholder="Nombre" v-model="formData.nombre">
                <span v-if="errors.nombre" class="text-danger">{{ errors.nombre.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="text" class="p-2" placeholder="Usuario" v-model="formData.usuario">
                <span v-if="errors.usuario" class="text-danger">{{ errors.usuario.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="email" class="p-2" placeholder="Email" v-model="formData.email">
                <span v-if="errors.email" class="text-danger">{{ errors.email.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="password" class="p-2" placeholder="Contraseña" v-model="formData.contraseña">
                <span v-if="errors.contraseña" class="text-danger">{{ errors.contraseña.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="password" class="p-2" placeholder="Confirmar contraseña" v-model="formData.confirmar_contraseña">
                <span v-if="errors.confirmar_contraseña" class="text-danger">{{ errors.confirmar_contraseña.toString() }}</span>
            </p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <button @click="registerUser" class="btn btn-primary">Registrarse</button>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <a class="pointer" @click="login">Ya tengo una cuenta</a>
                </div>
            </div>
        </div>
    </div>                      
</template>

<script>
export default {
    data() {
        return {
            formData: {
                nombre: '',
                usuario: '',
                email: '',
                contraseña: '',
                confirmar_contraseña: ''
            },
            errors: {}
        }
    },
    mounted() {
        document.title = "Registro - What's new"
    },
    methods: {
        registerUser() {
            axios.post('/api/register', this.formData).then((response) => {
                console.log(response.data)
                this.formData.nombre = this.formData.usuario = this.formData.email = this.formData.contraseña = this.formData.confirmar_contraseña = ''
                this.$router.push('/login')
            }).catch((errors) => {
                this.errors = errors.response.data.errors
            });
        },
        login() {
            this.$emit('login');
        },
        cerrarRegistro() {
            this.$emit('cerrarRegistro');
        }
    }
}
</script>