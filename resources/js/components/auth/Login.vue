<template>
    <div>
        <div class="row justify-content-center align-items-center" style="height:80vh;">
            <div class="col-md-4">
                <div class="card background2">
                    <div class="card-header">Inicio sesión</div>
                    <div class="card-body">
                        <div class="form-group">
                            <input type="email" class="p-2" name="email" placeholder="Email" v-model="formData.email">
                            <p class="text-danger" v-text="errors.email"></p>
                        </div>
                        <div class="form-group">
                            <input type="password" class="p-2" name="password" placeholder="Contraseña" v-model="formData.password">
                            <p class="text-danger" v-text="errors.password"></p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button @click="handleLogin" class="btn btn-primary">Iniciar sesión</button>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <router-link to="/register">Crear nueva cuenta</router-link>
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
            },
        }
    },
    computed: {
        errors: {
            get() {
                return this.$store.state.currentUser.errors;
            }
        }
    },
    methods: {
        handleLogin() {
            this.$store.dispatch('currentUser/loginUser', this.formData);
        }
    }
}
</script>
