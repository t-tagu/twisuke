<template>
<div class="u-heightAdjust u-mainColor">
    <div class="c-container p-container">
      <div class="c-container__inner p-container__inner">
        <form class="c-form p-form" v-on:submit.prevent="login">
          <h1 class="p-form__title"><a class="p-form__toplink">{{ appName }}</a></h1>
          <h2 class="p-form__subtitle">ログイン</h2>
          <div class="c-form__box p-form__box u-mb25">
            <button type="submit" class="c-form__btn p-form__btn">
              <i class="fab fa-twitter p-form__icon"></i>ツイッターでログイン
            </button>
          </div>
          <div class="p-form__linkbox">
            <a class="p-form__link p-form__link--top" href="/">トップへ戻る</a>
          </div>
        </form>
      </div>
    </div>
    <footer class="l-footer p-footer u-ml0">
      <p>Copyright © {{ appName }}. All Rights Reserved</p>
    </footer>
  </div>
</template>

<script src="https://www.gstatic.com/firebasejs/7.22.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.22.1/firebase-auth.js"></script>
<script>
import axios from 'axios';
import firebase from "firebase";

let POPUP_CLOSED = 'ポップアップが閉じられました。';
let AUTH_REJECTED = '認証を拒否しました。';
let SERVICE_STOPPED = 'サービスの利用が停止されています。';
let POPUP_BLOCKED = '認証ポップアップがブロックされました。ポップアップをブロックしている場合は解除して下さい。';
let AUTH_DISABLED = '現在この認証方法はご利用頂けません。';
let ERROR_OCCURED = 'エラーが発生しました。しばらく時間をおいてお試しください。';
let NOT_VALID_ACCOUNT = '有効なアカウントではありません。';
let POPUP_WIDTH = 600; //twitter認証のポップアップとリダイレクトの境界線

var firebaseConfig = {
  apiKey: "AIzaSyDj-gESxnJE9R9iLfNwZRDXV6eB_g633Y8",
  authDomain: "twieve.firebaseapp.com",
  databaseURL: "https://twieve.firebaseio.com",
  projectId: "twieve",
  storageBucket: "twieve.appspot.com",
  messagingSenderId: "757104191968",
  appId: "1:757104191968:web:1f6ff8252527f995f7e385",
  measurementId: "G-ZGQTFHWXP1"
};
firebase.initializeApp(firebaseConfig);

export default {
  data: function(){
    return{
      appName: 'ツイ助'
    }
  },
  beforeRouteEnter: (to, from, next) => {
    axios.get('/auth_check').then(response=> { //認証済みならイベント作成画面へ
      if(response.data.authStatus){
        next('/schedule');
      }else{
        next();
      }
    });
  },
  mounted: function(){
    //リダイレクトしてログインをした場合、次にページが読み込まれた時に認証処理をおこなう
    let that = this;
    firebase.auth().getRedirectResult().then(function(result){
      if(result.user){
        that.firebaseTwitterAuthentication(result);
      }
    }).catch(function(e){
      console.log(e.message);
      that.firebaseErrorMessage(e);
    });
  },
  methods: {
    login: function(){
      let provider = new firebase.auth.TwitterAuthProvider();
      if(window.innerWidth <= POPUP_WIDTH){
        firebase.auth().signInWithRedirect(provider);
      }else{
        let that = this;
        firebase.auth().signInWithPopup(provider).then(function(result) {
          that.firebaseTwitterAuthentication(result);
        }).catch(function(error) {
          that.firebaseErrorMessage(e);
        });
      }
    },
    firebaseTwitterAuthentication: function(result){ //firebaseのtwitter認証の結果
      let token = result.credential.accessToken;
      let secret = result.credential.secret;
      let id = result.additionalUserInfo.profile.id_str;
      let user = result.user;
      if(user){
        axios.post('/make_user_data',{
          accountName: result.additionalUserInfo.username,
          displayName: user.displayName,
          photoURL: user.photoURL,
          accessToken: token,
          accessTokenSecret: secret,
          twitterIdStr: id
        }).then(response => { //ツイッターログイン完了
          this.$router.push('/schedule');
        }).catch((e) => { //エラー処理
          this.handleErrors({e : e, router : this.$router});
        });
      }else{
        alert(NOT_VALID_ACCOUNT);
      }
    },
    firebaseErrorMessage: function(e){
      switch(e.code) {
        case 'auth//cancelled-popup-request':
        case 'auth/popup-closed-by-user':
          alert(POPUP_CLOSED);
          break;
        case 'auth/user-cancelled':
          alert(AUTH_REJECTED);
          break;
        case 'auth/user-disabled':
          alert(SERVICE_STOPPED);
          break;
        case 'auth/popup-blocked':
          alert(POPUP_BLOCKED);
          break;
        case 'auth/operation-not-supported-in-this-environment':
        case 'auth/auth-domain-config-required':
        case 'auth/operation-not-allowed':
        case 'auth/unauthorized-domain':
          alert(AUTH_DISABLED);
          break;
        default:
          alert(ERROR_OCCURED);
          break;
      }
    }
  }
}
</script>
