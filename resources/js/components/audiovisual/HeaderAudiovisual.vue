<template>
    <div class="width">
        <div class="headerMedia">
            <h1 style="width: 100%">{{ audiovisual.titulo }}</h1>
            <div class="itemsHeaderMedia">
                <div style="padding-right: 20px">
                    <span v-if="audiovisual.puntuacion" class="item">{{ audiovisual.puntuacion }} / 5.0</span>
                    <span v-else class="item">--- / 5.0</span>
                    <b-icon icon="star-fill"></b-icon>
                </div>
                <span class="item" style="color: green" v-if="audiovisual.tipoAudiovisual_id === 2 && audiovisual.estado === 1">En emisión</span>
                <span class="item" style="color: gray" v-else-if="audiovisual.tipoAudiovisual_id === 2 && audiovisual.estado === 2">Finalizada</span>
                <span class="item" v-if="audiovisual.tipoAudiovisual_id === 1">Película</span>
                <span class="item" v-else>Serie</span>
                <span class="item" v-if="audiovisual.tipoAudiovisual_id === 2 && audiovisual.numeroTemporadas === 1">1 temporada</span>
                <span class="item" v-else-if="audiovisual.tipoAudiovisual_id === 2 && audiovisual.numeroTemporadas > 1">{{ audiovisual.numeroTemporadas }} temporadas</span>
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