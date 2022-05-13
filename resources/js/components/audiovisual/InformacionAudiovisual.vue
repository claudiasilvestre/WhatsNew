<template>
    <div class="list">
        <h5>Título original</h5>
        <p>{{ audiovisual.tituloOriginal }}</p>
        <h5>Estreno</h5>
        <p>{{ audiovisual.fechaLanzamiento }}</p>
        <h5>Sinopsis</h5>
        <p>{{ audiovisual.sinopsis }}</p>
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