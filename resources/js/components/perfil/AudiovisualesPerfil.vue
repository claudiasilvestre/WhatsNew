<template>
    <div>
        <div class="d-flex flex-wrap list">
            <div v-for="audiovisual in audiovisuales" :key="audiovisual.id" class="mr-2">
                <router-link :to="{ name: 'audiovisual', params: { id: audiovisual.audiovisual_id }}">
                    <img class="rounded" v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" width="175" height="250">
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
      audiovisuales: {
        required: true,
        type: Array
      }
    }
}
</script>