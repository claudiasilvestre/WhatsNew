<template>
    <div class="width">
        <div class="headerMedia">
            <h1 style="width: 100%">{{ audiovisual.titulo }}</h1>
            <div class="itemsHeaderMedia">
                <div style="padding-right: 20px">
                    <span class="item">{{ audiovisual.puntuacion }}</span>
                    <b-icon icon="star-fill"></b-icon>
                </div>
                <span class="item" v-if="audiovisual.tipoAudiovisual_id === 2 && audiovisual.numeroTemporadas === 1">1 temporada</span>
                <span class="item" v-else-if="audiovisual.tipoAudiovisual_id === 2 && audiovisual.numeroTemporadas > 1">{{ audiovisual.numeroTemporadas }} temporadas</span>
                <span class="item" v-if="audiovisual.tipoAudiovisual_id === 1">Película</span>
                <span class="item" v-else>Serie</span>
                <span class="item">{{ audiovisual.anno }}</span>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
      audiovisual: {
        required: true,
        type: Object
      }
    }
}
</script>