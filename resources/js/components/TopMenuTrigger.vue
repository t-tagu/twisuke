<!-- トップ画面のメニュートリガー -->
<template>
  <div class="p-top-trigger" v-on:click="toggle">
    <span class="p-top-trigger__line" v-bind:class="{'p-top-trigger__line--active': active}"></span>
    <span class="p-top-trigger__line" v-bind:class="{'p-top-trigger__line--active': active}"></span>
    <span class="p-top-trigger__line" v-bind:class="{'p-top-trigger__line--active': active}"></span>
  </div>
</template>

<script>
import { eventBus } from '../app';

  export default {
    data(){
      return {
        active: false
      }
    },
    mounted() {
      eventBus.$on('clickTopMenuLink', active => { //メニュー非表示を受け取る
        this.active = active;
      });
    },
    methods: {
      toggle(){
        this.active = !this.active;
        eventBus.$emit('toggleTopMenu', this.active);
      }
    }
  }
</script>
