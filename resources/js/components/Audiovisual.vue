<template>
    <div class="header">
        <info />
        <div class="width">
            <header-audiovisual />
            <menu-audiovisual />
        </div>
    </div>
</template>

<script>
import Info from './Info.vue'
import MenuAudiovisual from './MenuAudiovisual.vue'
import HeaderAudiovisual from './HeaderAudiovisual.vue'

export default {
    components: {
        Info,
        MenuAudiovisual,
        HeaderAudiovisual
    }
}
</script>