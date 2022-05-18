<template>
    <v-app>
        <div class="d-flex justify-content-between list">
            <div>
                <v-select
                    v-model="selected"
                    :items="temporadas"
                    :background-color="'#1e1e1e'"
                    item-text="nombre"
                    return-object
                    solo
                />
            </div>
            <div class="d-flex align-items-center">
                <b-button variant="dark">
                    Marcar temporada como vista
                <b-icon icon="check-circle"></b-icon></b-button>
            </div>
        </div>
    </v-app>
</template>

<script>
export default {
    props: {
      audiovisual: {
        required: true,
        type: Object
      }
    },
    data() {
        return {
            temporadas: [],
            selected: { nombre: 'Temporada 1' },
        }
    },
    created() {
        axios.get('/temporadas/'+this.audiovisual.id)
            .then(response => this.temporadas = response.data)
            .catch(error => { console.log(error.response) });
    },
}
</script>
