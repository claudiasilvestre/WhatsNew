<template>
    <div>
        <img v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" width="185" height="270"> 
    </div>
</template>

<script>
export default {
    props: {
        audiovisual: {
            type: Object,
            required: true
        }
    }
}
</script>