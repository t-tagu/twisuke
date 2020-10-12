<template>
  <div class="u-heightAdjust">
    <header class="l-header p-header">
      <div class="p-header__content">
        <h1 class="p-header__title">{{ appName }}</h1>
        <header-navigation></header-navigation>
        <menu-trigger></menu-trigger>
      </div>
      <trigger-menu></trigger-menu>
    </header>
    <main class="l-main">
      <sidebar></sidebar>
      <div class="p-content">
        <router-view></router-view>
      </div>
    </main>
    <footer class="l-footer p-footer">
      <p>Copyright ©︎ {{ appName }}. All Rights Reserved</p>
    </footer>
  </div>
</template>

<script>

export default {
  name: 'Member',
  data: function(){
    return{
      appName: 'ツイ助'
    }
  }
}

</script>
