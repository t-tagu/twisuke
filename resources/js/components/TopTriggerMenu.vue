<!-- トップ画面で表示されるメニュー -->
<template>
  <nav class="p-top-trigger-menu" v-bind:class="{'p-top-trigger-menu--active': active }" v-if="isShow">
    <ul class="p-top-trigger-menu__list">
      <li class="p-top-trigger-menu__item"><a class="p-top-trigger-menu__link" href="#about" v-on:click="clickTopMenuLink" v-smooth-scroll="{ duration: 1000, offset: -60 }">ツイ助とは</a></li>
      <li class="p-top-trigger-menu__item"><a class="p-top-trigger-menu__link" href="#feature" v-on:click="clickTopMenuLink" v-smooth-scroll="{ duration: 1000, offset: -60 }">機能紹介</a></li>
      <li class="p-top-trigger-menu__item"><a class="p-top-trigger-menu__link" href="/login">ログイン</a></li>
    </ul>
  </nav>
</template>

<script>
import { eventBus } from '../app';

let SHOW_WIDTH = 980; //メニューの表示・非表示の境界線

  export default {
    data: function(){
      return {
        active: false,
        isShow: false
      }
    },
    mounted: function(){
      window.addEventListener('resize', this.handleResize);
      this.handleResize();

      eventBus.$on("toggleTopMenu", active => { //メニュー表示・非表示切り替え
        this.active = active;
      });
    },
    methods: {
      clickTopMenuLink: function(){ //メニュー閉じる
        this.active = false;
        eventBus.$emit('clickTopMenuLink', false);
      },
      handleResize: function(){
        if(window.innerWidth <= SHOW_WIDTH){
          this.isShow = true;
        }else{
          this.isShow = false;
        }
      }
    }
  }
</script>
