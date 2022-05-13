<template>
    <div>
        <img class="rounded" v-bind:src="audiovisual.cartel" v-bind:alt="audiovisual.titulo" width="200" height="300">
        <div class="buttons width">
            <b-button variant="danger" class="m-1"><b-icon icon="clock"></b-icon>
                Pendiente
            </b-button>
            <b-button variant="warning" class="m-1"><b-icon icon="eye"></b-icon>
                Seguir
            </b-button>
            <b-button variant="info" class="m-1"><b-icon icon="check2"></b-icon>
                Vista
            </b-button>
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