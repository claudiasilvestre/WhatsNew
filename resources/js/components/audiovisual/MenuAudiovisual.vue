<template>
    <div class="list">
        <b-tabs>
            <b-tab title="Información" active><informacion-audiovisual :audiovisual="audiovisual" /></b-tab>
            <b-tab v-if="audiovisual.tipoAudiovisual_id === 2" title="Capítulos" active><capitulos-audiovisual /></b-tab>
            <b-tab title="Comentarios"></b-tab>
        </b-tabs>
    </div>
</template>

<script>
import InformacionAudiovisual from './InformacionAudiovisual.vue'
import CapitulosAudiovisual from './CapitulosAudiovisual.vue'

export default {
    components: {
        InformacionAudiovisual,
        CapitulosAudiovisual
    },
    props: {
      audiovisual: {
        required: true,
        type: Object
      }
    }
}
</script>