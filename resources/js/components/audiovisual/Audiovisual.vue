<template>
    <v-app>
        <div>
            <app-header />
            <div v-if="!loading" class="header">
                <aside-audiovisual :audiovisual="audiovisual" />
                <div class="width">
                    <header-audiovisual :audiovisual="audiovisual" />
                    <menu-audiovisual :audiovisual="audiovisual" />
                </div>
            </div>
        </div>
    </v-app>
</template>

<script>
import Header from '../layouts/Header.vue'
import AsideAudiovisual from './AsideAudiovisual.vue'
import HeaderAudiovisual from './HeaderAudiovisual.vue'
import MenuAudiovisual from './MenuAudiovisual.vue'

export default {
    components: {
        'app-header': Header,
        AsideAudiovisual,
        HeaderAudiovisual,
        MenuAudiovisual,
    },
    data() {
        return {
            id: this.$route.params.id,
            audiovisual: {},
            loading: true,
        }
    },
    created() {
        axios.get('/api/audiovisuales/'+this.id)
            .then(response => this.audiovisual = response.data[0])
            .catch(error => { console.log(error.response) })
            .finally(() => this.loading = false);
    },
}
</script>