<template>
    <div>
        <img class="rounded" v-bind:src="capitulo.cartel" v-bind:alt="capitulo.nombre" width="300" height="200">
    </div>
</template>

<script>
export default {
    props: {
      capitulo: {
        required: true,
        type: Object
      }
    }
}
</script>