<template>
  <div class="u-heightAdjust u-mainColor">
    <div class="p-page404__container">
      <div class="p-page404__inner">
        <h1 class="p-page404__title">
          <span class="p-page404__title--main">{{ mainTitle }}</span>
          <span class="p-page404__title--sub">{{ subTitle }}</span>
        </h1>
        <p class="p-page404__warning">
          {{ sentense }}
        </p>
        <div class="p-page404__link-box">
          <a class="p-page404__link" href="/">{{ appName }}トップへ</a>
        </div>
      </div>
    </div>
    <footer class="l-footer p-footer u-ml0">
      <p>Copyright © {{ appName }}. All Rights Reserved</p>
    </footer>
  </div>
</template>

<script>

export default {
  data(){
    return {
      mainTitle: "404",
      subTitle: "Not Found",
      sentense: "お探しのページは存在しないか、一時的にアクセスができない状況にある可能性があります。 また、URL、ファイル名にタイプミスがないか再度ご確認ください。",
      appName: "ツイ助"
    }
  }
}
</script>
