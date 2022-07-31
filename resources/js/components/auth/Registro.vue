<template>
    <div>
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Registro</h5>
            <b-icon icon="x-lg" @click="cerrarRegistro" class="pointer"></b-icon>
        </div>
        <div class="card-body">
            <div class="form-group">
                <input type="text" class="p-2" name="nombre" placeholder="Nombre" v-model="formData.nombre">
                <p class="text-danger" v-text="errors.nombre"></p>
            </div>
            <div class="form-group">
                <input type="text" class="p-2" name="usuario" placeholder="Usuario" v-model="formData.usuario">
                <p class="text-danger" v-text="errors.usuario"></p>
            </div>
            <div class="form-group">
                <input type="email" class="p-2" name="email" placeholder="Email" v-model="formData.email">
                <p class="text-danger" v-text="errors.email"></p>
            </div>
            <div class="form-group">
                <input type="password" class="p-2" name="password" placeholder="Contraseña" v-model="formData.password">
                <p class="text-danger" v-text="errors.password"></p>
            </div>
            <div class="form-group">
                <input type="password" class="p-2" name="password_confirmation" placeholder="Confirmar contraseña" v-model="formData.password_confirmation">
                <p class="text-danger" v-text="errors.password_confirmation"></p>
            </div>

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
                password: '',
                password_confirmation: ''
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
                this.formData.nombre = this.formData.usuario = this.formData.email = this.formData.password = this.formData.password_confirmation = ''
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