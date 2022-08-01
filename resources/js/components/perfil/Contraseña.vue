<template>
    <div class="row justify-content-center align-items-center" style="height:80vh;">
        <div class="col-md-4">
            <div class="card background2">
                <div class="card-header">Contraseña</div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="password" class="p-2" name="current_password" placeholder="Contraseña actual" v-model="formData.current_password">
                        <p class="text-danger" v-text="errors.current_password"></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="p-2" name="password" placeholder="Nueva contraseña" v-model="formData.password">
                        <p class="text-danger" v-text="errors.password"></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="p-2" name="password_confirmation" placeholder="Confirmar nueva contraseña" v-model="formData.password_confirmation">
                        <p class="text-danger" v-text="errors.password_confirmation"></p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button @click="guardarCambios" class="btn btn-primary">Cambiar contraseña</button>
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
    methods: {
        guardarCambios() {
            axios.put('/api/guardar-password', this.formData).then(() => {
                this.$router.push('/perfil')
            }).catch((errors) => {
                this.errors = errors.response.data.errors
            });
        }
    }
}
</script>