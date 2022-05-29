<template>
    <div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Inicio sesión</div>
                    <div class="card-body">
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

                        <div ckass="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button @click="handleLogin" class="btn btn-primary">Iniciar sesión</button>
                                </div>
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
                email: '',
                password: '',
                device_name: 'browser'
            },
            errors: {}
        }
    },
    methods: {
        handleLogin() {
            axios.get('/sanctum/csrf-cookie').then(response => {
                axios.post('/api/login', this.formData).then((response) => {
                    this.$router.push('/')
                }).catch((errors) => {
                    this.errors = errors.response.data.errors
                })
            });
        }
    }
}
</script>
