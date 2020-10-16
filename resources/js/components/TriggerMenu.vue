<!-- 画面が小さくなった時のメニュー -->
<template>
  <nav class="p-menu" v-bind:class="{'p-menu--active': active}" v-if="isShow">
    <ul class="p-menu__account-list">
      <li class="p-menu__account-item">
        <div class="p-menu__account-item-inner">
          <div class="p-menu__profile-container">
            <div class="p-menu__icon-container">
              <img class="p-menu__icon" :src="img">
            </div>
            <div class="p-menu__name-container">
              <span class="p-menu__display-name">{{ displayName }}</span>
              <span class="p-menu__account-name">@{{ accountName }}</span>
            </div>
          </div>
        </div>
      </li>
    </ul>
    <ul class="p-menu__list">
      <li class="p-menu__item">
        <router-link class="p-menu__link" :to="{ name: 'schedule' }" v-on:click.native="clickLink">
          イベント作成
        </router-link>
      </li>
      <li class="p-menu__item">
        <router-link class="p-menu__link" :to="{ name: 'my_event', params: { twitter_id: twitterId } }" v-on:click.native="clickLink">
          作成イベント確認
        </router-link>
      </li>
      <li class="p-menu__item">
        <a class="p-menu__link" href="/logout">ログアウト</a>
      </li>
    </ul>
  </nav>
</template>

<script>
import axios from 'axios';
import { eventBus } from '../app';

let SHOW_WIDTH = 980; //メニューの表示・非表示の境界線

  export default {
    data: function(){
      return {
        active: false,
        isShow: false,
        accountName: '',
        displayName: '',
        img: '',
        twitterId: ''
      }
    },
    created: function(){
      window.addEventListener('resize', this.handleResize);
      this.handleResize()
    },
    mounted: function(){
      this.getAccountData(); //使用中のアカウントデータの取得
      eventBus.$on('toggle', active => { //メニュー表示・非表示切り替え
        this.active = active;
      });
    },
    destroyed: function(){
      window.removeEventListener('resize', this.handleResize);
    },
    methods: {
      getAccountData: function(){
        axios.get('/get_account_data').then(response => {
          this.accountName = response.data.accountName;
          this.displayName = response.data.displayName;
          this.img = response.data.photoUrl;
          this.twitterId = response.data.twitterId;
        }).catch((e) => {
          this.handleErrors({e : e, router : this.$router});
        });
      },
      clickLink: function(){
        this.active = false;
        eventBus.$emit('clickLink', false);
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
