<template>
  <div class="p-trigger" v-on:click="toggle">
    <span class="p-trigger__line" v-bind:class="{'p-trigger__line--active': active}"></span>
    <span class="p-trigger__line" v-bind:class="{'p-trigger__line--active': active}"></span>
    <span class="p-trigger__line" v-bind:class="{'p-trigger__line--active': active}"></span>
  </div>
</template>

<script>
import { eventBus } from '../app';

export default {
  name: 'MenuTrigger',
  data: function(){
    return {
      active: false
    }
  },
  mounted: function(){
    eventBus.$on('clickLink', active => { //メニュー非表示
      this.active = active;
    });
  },
  methods: {
    toggle: function(){
      this.active = !this.active;
      eventBus.$emit('toggle', this.active);
    }
  }
}
</script>
