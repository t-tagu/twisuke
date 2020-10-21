<!-- トップ画面で表示されるメニュー -->
<template>
  <nav class="p-top-trigger-menu" v-bind:class="{'p-top-trigger-menu--active': active }" v-if="isShow">
    <ul class="p-top-trigger-menu__list">
      <div v-show="isOffset80">
        <li class="p-top-trigger-menu__item"><a class="p-top-trigger-menu__link" href="#about" v-on:click="clickTopMenuLink" v-smooth-scroll="{ duration: scrollDuration, offset: offset80 }">{{ appName }}</a></li>
        <li class="p-top-trigger-menu__item"><a class="p-top-trigger-menu__link" href="#feature" v-on:click="clickTopMenuLink" v-smooth-scroll="{ duration: scrollDuration, offset: offset80 }">{{ funcIntroduction }}</a></li>
      </div>
      <div v-show="!isOffset80">
        <li class="p-top-trigger-menu__item"><a class="p-top-trigger-menu__link" href="#about" v-on:click="clickTopMenuLink" v-smooth-scroll="{ duration: scrollDuration, offset: offset60 }">{{ appName }}2</a></li>
        <li class="p-top-trigger-menu__item"><a class="p-top-trigger-menu__link" href="#feature" v-on:click="clickTopMenuLink" v-smooth-scroll="{ duration: scrollDuration, offset: offset60 }">{{ funcIntroduction }}2</a></li>
      </div>
      <li class="p-top-trigger-menu__item"><a class="p-top-trigger-menu__link" href="/login">ログイン</a></li>
    </ul>
  </nav>
</template>

<script>
import { eventBus } from '../app';

let SHOW_WIDTH = 980; //メニューの表示・非表示の切り替え
let OFFSET_BORDER = 720; //スクロールオフセットの切り替え

  export default {
    data: function(){
      return {
        appName: 'ツイ助とは',
        funcIntroduction: '機能紹介',
        active: false,
        isShow: false,
        scrollDuration: 1000,
        offset80: -80,
        offset60: -60,
        isOffset80: true
      }
    },
    created: function(){
      window.addEventListener('resize', this.handleResize);
      this.handleResize();
    },
    mounted: function(){
      eventBus.$on("toggleTopMenu", active => { //メニュー表示・非表示切り替え
        this.active = active;
      });
    },
    destroyed: function(){
      window.removeEventListener('resize', this.handleResize);
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

        if(window.innerWidth <= OFFSET_BORDER){
          this.isOffset80 = false;
        }else{
          this.isOffset80 = true;
        }
      }
    }
  }
</script>
