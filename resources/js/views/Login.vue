<template>
  <div class="u-heightAdjust u-mainColor">
    <div class="c-container p-container">
      <div class="c-container__inner p-container__inner">
        <form class="c-form p-form" v-on:submit.prevent="login">
          <h1 class="p-form__title"><a class="p-form__toplink" href="/">{{ appName }}</a></h1>
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
import firebase from "firebase/app";
import "firebase/auth";
import Firebase from '../firebase';
import store from '../store';

const POPUP_CLOSED = 'ポップアップが閉じられました。';
const AUTH_REJECTED = '認証を拒否しました。';
const SERVICE_STOPPED = 'サービスの利用が停止されています。';
const POPUP_BLOCKED = '認証ポップアップがブロックされました。ポップアップをブロックしている場合は解除して下さい。';
const AUTH_DISABLED = '現在この認証方法はご利用頂けません。';
const ERROR_OCCURED = 'エラーが発生しました。しばらく時間をおいてお試しください。';
const NOT_VALID_ACCOUNT = '有効なアカウントではありません。';
const POPUP_WIDTH = 600; //twitter認証のポップアップとリダイレクトの境界線

export default {
  data(){
    return{
      appName: 'ツイ助'
    }
  },
  beforeRouteEnter: (to, from, next) => {
    if(store.getters.isSignedIn){
      next('/schedule');
    }else{
      next();
    }
  },
  mounted(){
    //リダイレクトしてログインをした場合、次にページが読み込まれた時に認証処理をおこなう
    const that = this;
    firebase.auth().getRedirectResult().then((result) => {
      if(result.user){
        that.firebaseTwitterAuthentication(result);
      }
    }).catch((e) => {
      that.firebaseErrorMessage(e);
    });
  },
  methods: {
    login(){
      const provider = new firebase.auth.TwitterAuthProvider();
      if(window.innerWidth <= POPUP_WIDTH){
        firebase.auth().signInWithRedirect(provider);
      }else{
        const that = this;
        firebase.auth().signInWithPopup(provider).then((result) => {
          that.firebaseTwitterAuthentication(result);
        }).catch((e) => {
          that.firebaseErrorMessage(e);
        });
      }
    },
    firebaseTwitterAuthentication(result){ //firebaseのtwitter認証の結果
      let user = result.user;
      if(user){
        const token = result.credential.accessToken;
        const secret = result.credential.secret;
        const twitterId = user.providerData[0].uid;
        const userInfo = {
            'displayName': user.displayName,
            'photoURL': user.photoURL,
            'twitterId': twitterId
        }
        const isSignedIn = user.uid ? true : false;

        store.commit('setUserInfo', userInfo);
        store.commit('setUserLoginStatus', isSignedIn);

        axios.post('/sign_up',{
          twitterIdStr: twitterId,
          accessToken: token,
          accessTokenSecret: secret
        }).then((response) => {
          if(store.getters.isSignedIn){
            this.$router.go('/schedule');
          }
        }).catch((e) => { //エラー処理
          this.handleErrors({e : e, router : this.$router});
        });
      }else{
        alert(NOT_VALID_ACCOUNT);
      }
    },
    firebaseErrorMessage(e){
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
