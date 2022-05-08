<template>
    <div class="width">
        <div class="headerMedia">
            <h1 style="width: 100%">{{ $route.params.audiovisual.titulo }}</h1>
            <div class="itemsHeaderMedia">
                <div style="padding-right: 20px">
                    <span class="item">{{ $route.params.audiovisual.puntuacion }}</span>
                    <b-icon icon="star-fill"></b-icon>
                </div>
                <span class="item" v-if="$route.params.audiovisual.tipoAudiovisual_id=1">Película</span>
                <span class="item" v-else>Serie</span>
                <span class="item">{{ $route.params.audiovisual.anno }}</span>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    
}
</script>