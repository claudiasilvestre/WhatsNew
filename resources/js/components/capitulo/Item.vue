<template>
    <div>
        <img class="rounded" v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" width="200" height="300">
        <div>
            
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