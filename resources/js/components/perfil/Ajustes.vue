<template>
    <div class="row justify-content-center align-items-center m-4" style="height:80vh;">
        <div class="col-md-4">
            <div class="card background2">
                <div class="card-header">Información personal</div>
                <div class="card-body">
                    <div class="form-group">
                        <label id="imagen_perfil">Imagen de perfil</label>
                        <p>
                            <img class="roundedPerfil m-1" v-bind:src="formData.foto" width="125" height="125" v-bind:alt="formData.nombre">
                            <input type="file" @change="previewFile" accept="image/png, image/jpeg, image/jpg">
                        </p>
                    </div>
                    <p class="form-group">
                        <input type="text" @keydown.enter="guardarCambios()" ref="nombre" class="p-2" placeholder="Nombre" v-model="formData.nombre">
                        <span v-if="errors.nombre" class="text-danger">{{ errors.nombre.toString() }}</span>
                    </p>
                    <p class="form-group">
                        <input type="text" @keydown.enter="guardarCambios()" ref="usuario" class="p-2" placeholder="Usuario" v-model="formData.usuario">
                        <span v-if="errors.usuario" class="text-danger">{{ errors.usuario.toString() }}</span>
                    </p>
                    <p class="form-group">
                        <input type="email" @keydown.enter="guardarCambios()" ref="email" class="p-2" placeholder="Email" v-model="formData.email">
                        <span v-if="errors.email" class="text-danger">{{ errors.email.toString() }}</span>
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button @click="guardarCambiosBtn()" class="btn btn-info">Guardar cambios</button>
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
            usuario_id: this.$route.params.idPersona,
            formData: {},
            errors: {},
            imagen: null,
        }
    },
    created() {
        axios.get('/api/info-usuario/'+this.usuario_id)
            .then(response => {
                this.formData = response.data;
            })
            .catch((errors) => {
                console.log(errors.response.data)
            });
    },
    methods: {
        guardarCambiosBtn() {
            let fd = new FormData();
            fd.append('imagen', this.imagen);
            fd.append('nombre', this.formData.nombre);
            fd.append('usuario', this.formData.usuario);
            fd.append('email', this.formData.email);

            axios.post('/api/guardar-informacion', fd).then(() => {
                this.$router.push('/perfil/'+this.usuario_id)
            }).catch((errors) => {
                this.errors = errors.response.data.errors
            });
        },
        guardarCambios() {
            if (!this.formData.nombre)
                this.$refs.nombre.focus()
            else if (!this.formData.usuario)
                this.$refs.usuario.focus()
            else if (!this.formData.email)
                this.$refs.email.focus()
            else {
                let fd = new FormData();
                fd.append('imagen', this.imagen);
                fd.append('nombre', this.formData.nombre);
                fd.append('usuario', this.formData.usuario);
                fd.append('email', this.formData.email);

                axios.post('/api/guardar-informacion', fd).then(() => {
                    this.$router.push('/perfil/'+this.usuario_id)
                }).catch((errors) => {
                    this.errors = errors.response.data.errors
                });
            }
        },
        previewFile(event) {
            this.imagen = event.target.files[0]
        }
    }
}
</script>