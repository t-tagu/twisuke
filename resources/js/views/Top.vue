<template>
  <div>
    <top-header></top-header>
    <main class="l-main p-hero__banar">
      <section class="p-hero">
        <div class="p-hero__container">
          <div class="p-hero__content">
            <h2 class="p-hero__title">フォロワーとのスケジュール調整を<br class="p-hero__title--newline">ワンストップで</h2>
            <p class="p-hero__description">{{ appName }}はツイッターのフォロワー間でのイベントや集まりのスケジュール調整を<br class="p-hero__description--newline">ワンストップでおこなうことができるウェブサービスです。</p>
            <a class="p-hero__link" href="/login">{{ appName }}をすぐ使う</a>
          </div>
        </div>
      </section>
      <div class="u-bg-white-color">
        <section class="p-top__container" id="about">
          <h2 class="p-top__container-title p-top__container-title--color">{{ appName }}とは何ですか？</h2>
          <div class="p-top__container-content">
            <div class="p-top__image-wrapper">
              <img class="p-top__image" src="/image/about.jpg">
            </div>
            <p class="p-top__about">
              {{ appName }}はツイッターのフォロワー間のスケジュール調整を効率的におこなうウェブサービスです。<br>
              イベント作成からDMでのスケジュール調整URLの共有までをまとめて完了することができます。<br>
              {{ appName }}を使って効率的にスケジュール調整をしましょう！
            </p>
          </div>
        </section>
      </div>
      <div class="u-bg-main-color">
        <section class="p-top__container" id="feature">
          <h2 class="p-top__container-title">機能紹介</h2>
          <div class="p-top__container-content">
            <ul class="p-top__list">
              <li class="p-top__panel">
                <div class="p-top__panel-content">
                  <div class="p-top__icon-wrapper">
                    <i class="fab fa-twitter p-top__icon"></i>
                  </div>
                  <div class="p-top__panel-body">
                    <h3 class="p-top__panel-title">ツイッター連携</h3>
                    <p class="p-top__panel-description">メールアドレスなどの登録は不要！ツイッターと連携するだけで使用することができます！</p>
                  </div>
                </div>
              </li>
              <li class="p-top__panel">
                <div class="p-top__panel-content">
                  <div class="p-top__icon-wrapper">
                    <i class="fas fa-calendar-alt p-top__icon"></i>
                  </div>
                  <div class="p-top__panel-body">
                    <h3 class="p-top__panel-title">カレンダー入力</h3>
                    <p class="p-top__panel-description">候補日程の選択はカレンダーをクリックしながら選ぶだけで簡単に入力できます！</p>
                  </div>
                </div>
              </li>
              <li class="p-top__panel">
                <div class="p-top__panel-content">
                  <div class="p-top__icon-wrapper">
                    <i class="fas fa-envelope p-top__icon"></i>
                  </div>
                  <div class="p-top__panel-body">
                    <h3 class="p-top__panel-title">DM送信</h3>
                    <p class="p-top__panel-description">スケジュール調整をお願いしたいフォロワーにはDMでまとめて1０人までURLを送ることができます！</p>
                  </div>
                </div>
              </li>
              <li class="p-top__panel">
                <div class="p-top__panel-content">
                  <div class="p-top__icon-wrapper">
                    <i class="fas fa-list-alt p-top__icon"></i>
                  </div>
                  <div class="p-top__panel-body">
                    <h3 class="p-top__panel-title">イベント確認</h3>
                    <p class="p-top__panel-description">自分が作ったイベントについては一覧で確認可能！わざわざメモっておく必要がありません！</p>
                  </div>
                </div>
              </li>
              <li class="p-top__panel">
                <div class="p-top__panel-content">
                  <div class="p-top__icon-wrapper">
                    <i class="fas fa-pen-fancy p-top__icon"></i>
                  </div>
                  <div class="p-top__panel-body">
                    <h3 class="p-top__panel-title">出欠入力</h3>
                    <p class="p-top__panel-description">出欠は○△×の三択ボタンをクリックするだけ！伝えたいことがあればコメントも入力できます！</p>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </section>
      </div>
    </main>
    <footer class="l-footer p-footer u-ml0">
      <p>Copyright © {{ appName }}. All Rights Reserved</p>
    </footer>
  </div>
</template>

<script>

export default {
  name: 'Top',
  data: function(){
    return {
      appName: 'ツイ助'
    }
  },
}

</script>
