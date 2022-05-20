<template>
    <div>
        <div v-for="capitulo in capitulos" :key="capitulo.id" class="list d-flex flex-row background2 mb-2">
            <router-link :to="{ name: 'capitulo', params: { idAudiovisual: idAudiovisual, idCapitulo: capitulo.id }}">
                <img class="rounded" v-bind:src="capitulo.cartel" v-bind:alt="capitulo.nombre" width="250" height="140">
            </router-link>
            <div class="w-100 pl-4">
                <router-link :to="{ name: 'capitulo', params: { idAudiovisual: idAudiovisual, idCapitulo: capitulo.id }}">
                    <h5>{{ capitulo.nombre }}</h5>
                </router-link>
                <p>{{ capitulo.sinopsis }}</p>
                <div class="d-flex justify-content-end">
                    <b-button variant="dark">
                        Visto
                    <b-icon icon="check-circle"></b-icon></b-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
      capitulos: {
        required: true,
        type: Array
      },
      idAudiovisual: {
        required: true,
        type: Number
      }
    },
}
</script>