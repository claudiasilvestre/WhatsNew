<template>
    <div>
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Registro</h5>
            <b-icon icon="x-lg" @click="cerrarRegistro" class="pointer"></b-icon>
        </div>
        <div class="card-body">
            <p class="form-group">
                <input type="text" @keydown.enter="registerUser()" ref="nombre" class="p-2" placeholder="Nombre" v-model="formData.nombre">
                <span v-if="errors.nombre" class="text-danger">{{ errors.nombre.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="text" @keydown.enter="registerUser()" ref="usuario" class="p-2" placeholder="Usuario" v-model="formData.usuario">
                <span v-if="errors.usuario" class="text-danger">{{ errors.usuario.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="email" @keydown.enter="registerUser()" ref="email" class="p-2" placeholder="Email" v-model="formData.email">
                <span v-if="errors.email" class="text-danger">{{ errors.email.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="password" @keydown.enter="registerUser()" ref="password" class="p-2" placeholder="Contraseña" v-model="formData.password">
                <span v-if="errors.password" class="text-danger">{{ errors.password.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="password" @keydown.enter="registerUser()" ref="password_confirmation" class="p-2" placeholder="Confirmar contraseña" v-model="formData.password_confirmation">
                <span v-if="errors.password_confirmation" class="text-danger">{{ errors.password_confirmation.toString() }}</span>
            </p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <button @click="registerUserBtn()" class="btn btn-primary">Registrarse</button>
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
        registerUserBtn() {
            axios.post('/api/register', this.formData).then(() => {
                    this.formData.nombre = this.formData.usuario = this.formData.email = this.formData.password = this.formData.password_confirmation = ''
                    this.$emit('login');
                }).catch((errors) => {
                    this.errors = errors.response.data.errors
                });
        },
        registerUser() {
            if (!this.formData.nombre)
                this.$refs.nombre.focus()
            else if (!this.formData.usuario)
                this.$refs.usuario.focus()
            else if (!this.formData.email)
                this.$refs.email.focus()
            else if (!this.formData.password)
                this.$refs.password.focus()
            else if (!this.formData.password_confirmation)
                this.$refs.password_confirmation.focus()
            else
                axios.post('/api/register', this.formData).then(() => {
                    this.formData.nombre = this.formData.usuario = this.formData.email = this.formData.password = this.formData.password_confirmation = ''
                    this.$emit('login');
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