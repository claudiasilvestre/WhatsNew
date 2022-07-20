<template>
    <div class="list">
        <b-tabs>
            <b-tab title="Información" active><informacion-audiovisual :audiovisual="audiovisual" /></b-tab>
            <b-tab v-if="audiovisual.tipoAudiovisual_id === 2" title="Capítulos" active><capitulos :audiovisual="audiovisual" :cambioAside="cambioAside" /></b-tab>
            <b-tab title="Comentarios"><comentarios :audiovisual="audiovisual" /></b-tab>
        </b-tabs>
    </div>
</template>

<script>
import InformacionAudiovisual from './InformacionAudiovisual.vue'
import Capitulos from '../capitulo/Capitulos.vue'
import Comentarios from '../comentarios/Comentarios.vue'

export default {
    components: {
        InformacionAudiovisual,
        Capitulos,
        Comentarios
    },
    props: {
      audiovisual: {
        required: true,
        type: Object
      },
      cambioAside: {
        required: true,
        type: Boolean
      }
    }
}
</script>