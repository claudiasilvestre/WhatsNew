<template>
    <div class="header">
        <aside-audiovisual :audiovisual="audiovisual" />
        <div class="width">
            <header-audiovisual :audiovisual="audiovisual" />
            <menu-audiovisual :audiovisual="audiovisual" />
        </div>
    </div>
</template>

<script>
import AsideAudiovisual from './AsideAudiovisual.vue'
import MenuAudiovisual from './MenuAudiovisual.vue'
import HeaderAudiovisual from './HeaderAudiovisual.vue'

export default {
    components: {
        AsideAudiovisual,
        MenuAudiovisual,
        HeaderAudiovisual
    },
    data() {
        return {
            id: this.$route.params.id,
            audiovisual: {}
        }
    },
    created() {
        axios.get('/audiovisuales/'+this.id)
            .then(response => this.audiovisual = response.data[0])
            .catch(error => { console.log(error.response) });
    },
}
</script>