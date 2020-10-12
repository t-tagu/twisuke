<template>
  <div class="p-message">
    <div class="p-message__cover" v-bind:class="[isShow ===true ? 'p-message__cover--show' : 'p-message__cover--hide']">
      <span>{{ value }}</span>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'FlashMessage',
    props: {
      value: { type: String, required: true},
      isShow: { type: Boolean, required: true}
    }
  }
</script>
