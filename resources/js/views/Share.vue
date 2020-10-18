<template>
  <div>
    <loading :active.sync="isLoading"
          :can-cancel="false"
          :is-full-page="true"></loading>
    <content-inner :title='title' v-show="!isLoading">
      <flash-message :value='flashMessage' :isShow="isFlashShow"></flash-message>
      <loading :active.sync="isSendingDM"
            :can-cancel="false"
            :is-full-page="true"></loading>
      <div class="p-share__container">
        <div class="p-share__title-container p-share__subcontainer">
          <h2 class="p-share__subtitle">スケジュール入力URL</h2>
          <div class="p-share__url-container">
            <input class="p-share__url" type="text" v-model="eventURL" readonly>
            <button class="p-share__copy-button"
                    type="button"
                    v-clipboard:copy="eventURL"
                    v-clipboard:success="onCopy"
                    v-clipboard:error="onError"><i class="p-share__copy-icon far fa-copy"></i>
            </button>
          </div>
        </div>
        <div class="p-share__select-container p-share__subcontainer">
          <h2 class="p-share__subtitle">共有先を相互フォロー間から選択<span class="p-share__subtitle-attension">※１度に{{ maxAddress }}名まで送信可能</span></h2>
          <v-select
            class="p-share__select"
            :options="follower"
            label="name"
            :reduce="options => options.twitterId"
            v-model="selected"
            multiple>
            <template slot="option" slot-scope="option">
              <div class="p-share__select-content">
                <div class="p-share__icon-box">
                  <img class="p-share__icon" :src="option.image">
                </div>
                <span class="p-share__account-name u-block">
                  {{ option.name }}@{{ option.screenName }}
                </span>
              </div>
            </template>
          </v-select>
        </div>
        <div class="p-share__tweet-area">
          <div class="p-share__tweet-box">
            <textarea class="p-share__textarea" placeholder="DM内容を入力(必須300文字以内)" :maxlength="messageMaxLength" rows="5" v-model="message">{{ message }}</textarea>
          </div>
          <div class="p-share__tweet-count-box">
            <span class="p-share__count">{{ charaCount }}/{{ messageMaxLength }}</span>
          </div>
        </div>
        <button class="c-button p-share__button" v-on:click="send">DMを送信する</button>
        <div class="p-share__time-box">
          <span class="p-share__time-attension">※DM送信には30秒前後かかる場合がございます。</span>
        </div>
      </div>
    </content-inner>
  </div>
</template>

<script>
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

export default {
  name: 'Share',
  components: {
    Loading
  },
  data: function(){
    return {
      title: 'イベント共有',
      follower: [],
      message: '',
      messageMaxLength: 300,
      maxAddress: 10,
      selected: null,
      flashMessage: '',
      isFlashShow: false,
      isLoading: true,
      isSendingDM: false
    }
  },
  props: {
    event_id: String
  },
  computed: {
    charaCount: function() {
      return this.message.length;
    },
    eventURL :function(){
      return process.env.VUE_APP_URL+'/event/'+this.event_id;
    }
  },
  beforeRouteEnter: (to, from, next) => {
    axios.get('/auth_check').then(response=> { //認証済みならイベント作成画面へ
      if(response.data.authStatus){
        next();
      }else{
        next('/login');
      }
    });
  },
  mounted: function() {
    this.getFollower();
  },
  methods: {
    getFollower: function() { //DMの宛先を選択する(相互フォローを取得して渡す)
      axios.get('/get_user_twitter_mutual_followers').then(response=> {
        this.follower = response.data;
        setTimeout(() => {
          this.isLoading = false;
        }, 1000);
      }).catch((e) => {
        this.handleErrors({e : e, router : this.$router});
        setTimeout(() => {
          this.isLoading = false;
        }, 1000);
      });
    },
    onCopy: function(e) {
      alert('URLをコピーしました。');
    },
    onError: function(e) {
      alert('URLをコピーに失敗しました。');
    },
    send: function(){ //DMを送信する

      if(!this.selected.length){
        alert('送信先を選択して下さい。');
        return;
      }

      if(this.selected.length > this.maxAddress){
        alert('同時に送信できるのは'+this.maxAddress+'人までです。');
        return;
      }

      if(!this.message){
        alert('メッセージを入力下ください。');
        return;
      }

      if(this.message.length > this.messageMaxLength){
        alert('メッセージは'+this.messageMaxLength+'文字までです。');
        return;
      }

      let formData = new FormData();
      formData.append('message',this.message);
      formData.append('destination',this.selected.join(','));

      this.showFlashMessage('送信完了までしばらくお待ちください。');

      this.isSendingDM = true;

      axios.post('/send_direct_messages',formData,{
      }).then(response => {
        this.showFlashMessage('DMを送信しました。');
        this.isSendingDM = false;
      }).catch((e) => {
        this.handleErrors({e : e, router : this.$router});
        this.isSendingDM = false;
      });

    },
    showFlashMessage: function(message){
      this.isFlashShow = true;
      this.flashMessage = message;

      let that = this;
      setTimeout(function() {
        that.isFlashShow = false;
      }, 3000);

    }
  }
}
</script>
