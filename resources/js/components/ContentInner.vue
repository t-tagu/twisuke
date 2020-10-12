<template>
  <div class="p-content__inner">
    <div class="p-content__title-box">
      <h2 class="p-content__title">{{ title }}</h2>
    </div>
    <slot></slot>
  </div>
</template>

<script>

export default {
  name: 'ContentInner',
  props: {
    title: { type: String, required: true}
  }
}

</script>
