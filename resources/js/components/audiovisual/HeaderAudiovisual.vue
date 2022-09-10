<template>
    <div class="width">
        <div class="headerMedia">
            <img class="rounded img-fluid responsive-header-visibility mb-2" v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" width="150" height="250">
            <h1 class="mr-4" style="width: 100%">{{ audiovisual.titulo }}</h1>
            <div class="itemsHeaderMedia">
                <div style="padding-right: 20px">
                    <span v-if="audiovisual.puntuacion" class="item">{{ audiovisual.puntuacion }} / 5.0</span>
                    <span v-else class="item">--- / 5.0</span>
                    <b-icon icon="star-fill"></b-icon>
                </div>
                <div>
                    <span class="item" style="color: green" v-if="audiovisual.tipoAudiovisual_id === 2 && audiovisual.estado === 1">En emisión</span>
                    <span class="item" style="color: gray" v-else-if="audiovisual.tipoAudiovisual_id === 2 && audiovisual.estado === 2">Finalizada</span>
                    <span class="item" v-if="audiovisual.tipoAudiovisual_id === 1">Película</span>
                    <span class="item" v-else>Serie</span>
                    <span class="item" v-if="audiovisual.tipoAudiovisual_id === 2 && audiovisual.numeroTemporadas === 1">1 temporada</span>
                    <span class="item" v-else-if="audiovisual.tipoAudiovisual_id === 2 && audiovisual.numeroTemporadas > 1">{{ audiovisual.numeroTemporadas }} temporadas</span>
                    <span class="item">{{ audiovisual.anno }}</span>
                </div>
                <responsive-header class="responsive-header-visibility" :audiovisual="audiovisual" :usuarioActual="usuarioActual" :cambioAside="cambioAside" @comprobarCambioAside="comprobarCambioAside" @actualizarValoracion="actualizarValoracion"/>
            </div>
        </div>
    </div>
</template>

<script>
import ResponsiveHeader from './ResponsiveHeaderAudiovisual.vue'

export default {
    components: {
        ResponsiveHeader,
    },
    props: {
      audiovisual: {
        required: true,
        type: Object
      },
      usuarioActual: {
        required: true,
        type: Object
      },
      cambioAside: {
        type: Boolean
      }
    },
    methods: {
      comprobarCambioAside() {
        this.$emit('comprobarCambioAside');
      },
      actualizarValoracion() {
        this.$emit('actualizarValoracion');
      },
    }
}
</script>
