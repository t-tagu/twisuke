<!-- 画面を狭めてない時のヘッダーナビゲーション -->
<template>
  <nav class="p-header__nav">
    <ul class="p-header__menu">
      <li class="p-header__menu-item">
        <router-link class="p-header__menu-link" :to="{ name: 'schedule' }">イベント作成</router-link>
      </li>
      <li class="p-header__menu-item">
        <router-link class="p-header__menu-link" :to="{ name: 'my_event', params: { twitter_id: twitterId } }">作成イベント確認</router-link>
      </li>
    </ul>
    <a class="p-header__logout" href="/logout">ログアウト</a>
  </nav>
</template>

<script>
import axios from 'axios';

  const names = [
    { id:1, routeName: 'schedule', name: 'イベント作成'},
    { id:2, routeName: 'my_event', name: '作成イベント確認'}
  ];

  export default {
    name: 'HeaderNavigation',
    data: function() {
      return {
        names: names,
        twitterId: ''
      }
    },
    created(){
      this.getId();
    },
    methods: {
      getId: function(){
        axios.get('/get_session_twitter_id').then(response=> {
          this.twitterId = response.data.twitterId;
        }).catch((e) => {
          this.handleErrors({e : e, router : this.$router});
        });
      }
    }
  }

</script>
