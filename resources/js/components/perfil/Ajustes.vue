<template>
    <div class="row justify-content-center align-items-center" style="height:80vh;">
        <div class="col-md-4">
            <div class="card background2">
                <div class="card-header">Información personal</div>
                <div class="card-body">
                    <div class="form-group">
                        <label id="imagen_perfil">Imagen de perfil</label>
                        <p>
                            <img class="roundedPerfil" v-bind:src="formData.foto" width="125" height="125" v-bind:alt="formData.nombre" id="imagen_perfil" name="imagen_perfil">
                            <input type="file" @change="previewFile" accept="image/png, image/jpeg, image/jpg">
                        </p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="p-2" name="nombre" placeholder="Nombre" v-model="formData.nombre">
                        <p class="text-danger" v-text="errors.nombre"></p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="p-2" name="usuario" placeholder="Usuario" v-model="formData.usuario">
                        <p class="text-danger" v-text="errors.usuario"></p>
                    </div>
                    <div class="form-group">
                        <input type="email" class="p-2" name="email" placeholder="Email" v-model="formData.email">
                        <p class="text-danger" v-text="errors.email"></p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button @click="guardarCambios" class="btn btn-primary">Guardar cambios</button>
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
            file: '',
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
        guardarCambios() {
            this.formData.file = this.file;
            axios.put('/api/guardar-informacion', this.formData).then((response) => {
                console.log(response.data)
                this.$router.push('/perfil/'+this.usuario_id)
            }).catch((errors) => {
                console.log(errors.response.data)
                //this.errors = errors.response.data.errors
            });
        },
        previewFile(event) {
            this.file = event.target.files[0]
        }
    }
}
</script>