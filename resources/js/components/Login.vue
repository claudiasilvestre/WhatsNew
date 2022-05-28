<template>
    <div>
        <form action="#" @submit.prevent="handleLogin">
            <h3>Inicia sesión</h3>
            <input type="email" name="email" v-model="formData.email" placeholder="Email">
            <p class="text-danger" v-text="errors.email"></p>
            <input type="password" name="password" v-model="formData.password" placeholder="Contraseña">
            <p class="text-danger" v-text="errors.password"></p>
            <button type="submit" class="btn btn-primary">Iniciar sesión</button>
        </form>
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
