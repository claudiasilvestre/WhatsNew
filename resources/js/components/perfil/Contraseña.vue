<template>
    <div class="row justify-content-center align-items-center m-4" style="height:80vh;">
        <div class="col-md-4">
            <div class="card background2">
                <div class="card-header">Contraseña</div>
                <div class="card-body">
                    <p class="form-group">
                        <input type="password" @keydown.enter="guardarCambios()" ref="current_password" class="p-2" placeholder="Contraseña actual" v-model="formData.current_password">
                        <span v-if="errors.current_password" class="text-danger">{{ errors.current_password.toString() }}</span>
                    </p>
                    <p class="form-group">
                        <input type="password" @keydown.enter="guardarCambios()" ref="password" class="p-2" placeholder="Nueva contraseña" v-model="formData.password">
                        <span v-if="errors.password" class="text-danger">{{ errors.password.toString() }}</span>
                    </p>
                    <p class="form-group">
                        <input type="password" @keydown.enter="guardarCambios()" ref="password_confirmation" class="p-2" placeholder="Confirmar nueva contraseña" v-model="formData.password_confirmation">
                        <span v-if="errors.password_confirmation" class="text-danger">{{ errors.password_confirmation.toString() }}</span>
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button @click="guardarCambiosBtn()" class="btn btn-info">Cambiar contraseña</button>
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
            formData: {},
            errors: {}
        }
    },
    computed: {
        usuarioActual: {
            get() {
                return this.$store.state.usuarioActual.usuario;
            }
        }
    },
    methods: {
        guardarCambiosBtn() {
            axios.put('/api/guardar-password', this.formData).then(() => {
                    this.$router.push('/perfil/'+this.usuarioActual.id)
                }).catch((errors) => {
                    this.errors = errors.response.data.errors
                });
        },
        guardarCambios() {
            if (!this.formData.current_password)
                this.$refs.current_password.focus()
            else if (!this.formData.password)
                this.$refs.password.focus()
            else if (!this.formData.password_confirmation)
                this.$refs.password_confirmation.focus()
            else
                axios.put('/api/guardar-password', this.formData).then(() => {
                    this.$router.push('/perfil/'+this.usuarioActual.id)
                }).catch((errors) => {
                    this.errors = errors.response.data.errors
                });
        }
    }
}
</script>