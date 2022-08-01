<template>
    <div>
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Inicio sesión</h5>
            <b-icon icon="x-lg" @click="cerrarLogin" class="pointer"></b-icon>
        </div>
        <div class="card-body">
            <p class="form-group">
                <input type="email" class="p-2" placeholder="Email" v-model="formData.email">
            </p>
            <p class="form-group">
                <input type="password" class="p-2" placeholder="Contraseña" v-model="formData.password">
                <span v-if="errors.credenciales" class="text-danger">{{ errors.credenciales.toString() }}</span>
            </p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <button @click="handleLogin" class="btn btn-primary">Iniciar sesión</button>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <a class="pointer" @click="registro">Crear nueva cuenta</a>
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
    mounted() {
        document.title = "Inicio sesión - What's new"
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
        },
        registro() {
            this.$emit('registro');
        },
        cerrarLogin() {
            this.$emit('cerrarLogin');
        }
    }
}
</script>
