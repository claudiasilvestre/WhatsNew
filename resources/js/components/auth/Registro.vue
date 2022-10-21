<template>
    <div>
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Registro</h5>
            <b-icon icon="x-lg" @click="cerrarRegistro" class="pointer"></b-icon>
        </div>
        <div class="card-body">
            <p class="form-group">
                <input type="text" @keydown.enter="registroUsuario()" ref="nombre" class="p-2" placeholder="Nombre" v-model="formData.nombre">
                <span v-if="errors.nombre" class="text-danger">{{ errors.nombre.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="text" @keydown.enter="registroUsuario()" ref="usuario" class="p-2" placeholder="Usuario" v-model="formData.usuario">
                <span v-if="errors.usuario" class="text-danger">{{ errors.usuario.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="email" @keydown.enter="registroUsuario()" ref="email" class="p-2" placeholder="Correo" v-model="formData.email">
                <span v-if="errors.email" class="text-danger">{{ errors.email.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="password" @keydown.enter="registroUsuario()" ref="password" class="p-2" placeholder="Contraseña" v-model="formData.password">
                <span v-if="errors.password" class="text-danger">{{ errors.password.toString() }}</span>
            </p>
            <p class="form-group">
                <input type="password" @keydown.enter="registroUsuario()" ref="password_confirmation" class="p-2" placeholder="Confirmar contraseña" v-model="formData.password_confirmation">
                <span v-if="errors.password_confirmation" class="text-danger">{{ errors.password_confirmation.toString() }}</span>
            </p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <button @click="registroUsuarioBtn()" class="btn btn-info">Registrarse</button>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <a class="pointer color-a" @click="inicioSesion">Ya tengo una cuenta</a>
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
            formInicio: {
                email: '',
                password: ''
            },
            errors: {}
        }
    },
    mounted() {
        document.title = "Registro - WhatsNew"
    },
    methods: {
        registroUsuarioBtn() {
            axios.post('/api/registro', this.formData).then(() => {
                    this.formInicio.email = this.formData.email
                    this.formInicio.password = this.formData.password
                }).catch((errors) => {
                    this.errors = errors.response.data.errors
                }).finally(() => {
                    if (this.formInicio.email.length > 0 && this.formInicio.password.length > 0)
                        axios.get('/sanctum/csrf-cookie').then(response => {
                            axios.post('/api/inicio-sesion', this.formInicio).then((response) => {
                                window.location.replace("/home");
                            });
                        });
                });
        },
        registroUsuario() {
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
                axios.post('/api/registro', this.formData).then(() => {
                    this.formData.nombre = this.formData.usuario = this.formData.email = this.formData.password = this.formData.password_confirmation = ''
                    this.$emit('inicioSesion');
                }).catch((errors) => {
                    this.errors = errors.response.data.errors
                });
        },
        inicioSesion() {
            this.$emit('inicioSesion');
        },
        cerrarRegistro() {
            this.$emit('cerrarRegistro');
        }
    }
}
</script>