<template>
  <header class="l-header p-top-header" :class="{'p-top-header--sticky': position > 150}">
    <div class="p-top-header__content">
      <h1 class="p-top-header__title">{{ appName }}</h1>
      <ul class="p-top-menu">
        <li class="p-top-menu__item"><a class="p-top-menu__link" href="#about" v-smooth-scroll="{ duration: scrollDuration , offset: offset }">{{ appName }}とは</a></li>
        <li class="p-top-menu__item"><a class="p-top-menu__link" href="#feature" v-smooth-scroll="{ duration: scrollDuration , offset: offset }">機能紹介</a></li>
        <li class="p-top-menu__item"><a class="p-top-menu__link" href="/login">ログイン</a></li>
      </ul>
      <top-menu-trigger></top-menu-trigger>
    </div>
    <top-trigger-menu></top-trigger-menu>
  </header>
</template>

<script>
 export default {
   name: 'TopHeader',
   data() {
     return {
       position: 0,
       appName: 'ツイ助',
       scrollDuration: 1000,
       offset: -80
     }
   },
   mounted() {
     const that = this;
     document.onscroll = (e) => {
       that.position = document.documentElement.scrollTop || document.body.scrollTop;
     }
   }
 }
</script>
