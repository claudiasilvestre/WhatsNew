<template>
    <div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Registro</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" v-model="formData.nombre">
                            <p class="text-danger" v-text="errors.nombre"></p>
                        </div>
                        <div class="form-group">
                            <label for="usuario">Usuario</label>
                            <input type="text" name="usuario" v-model="formData.usuario">
                            <p class="text-danger" v-text="errors.usuario"></p>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" v-model="formData.email">
                            <p class="text-danger" v-text="errors.email"></p>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" v-model="formData.password">
                            <p class="text-danger" v-text="errors.password"></p>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation" v-model="formData.password_confirmation">
                            <p class="text-danger" v-text="errors.password_confirmation"></p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button @click="registerUser" class="btn btn-primary">Registrarse</button>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <router-link to="/login">Ya tengo una cuenta</router-link>
                            </div>
                        </div>
                    </div>
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
    methods: {
        registerUser() {
            axios.post('/api/register', this.formData).then((response) => {
                console.log(response.data)
                this.formData.nombre = this.formData.usuario = this.formData.email = this.formData.password = this.formData.password_confirmation = ''
                this.$router.push('/login')
            }).catch((errors) => {
                this.errors = errors.response.data.errors
            });
        }
    }
}
</script>