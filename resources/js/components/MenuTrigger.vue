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
  data(){
    return {
      active: false
    }
  },
  mounted(){
    //メニュー非表示、TriggerMenuのclickLinkをlistenさせる
    eventBus.$on('clickLink', (active) => {
      this.active = active;
    });
  },
  methods: {
    toggle(){
      this.active = !this.active;
      eventBus.$emit('toggle', this.active); //メニューの開閉の状態をメニューの開閉に合わせるて伝える
    }
  }
}
</script>
