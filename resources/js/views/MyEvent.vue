<template>
  <div>
    <loading :active.sync="isLoading"
          :can-cancel="false"
          :is-full-page="true"></loading>
    <content-inner :title='title' v-show="!isLoading">
      <div class="p-my-event__container">
        <div class="p-my-event__list-container" v-if="eventData.length">
          <ul class="p-my-event__list">
            <li class="p-my-event__list-item" v-for="(item, index) in eventData" v-bind:key="item.id">
              <accordion v-bind:title="item.name" v-bind:eventId="item.event_id"></accordion>
            </li>
          </ul>
        </div>
        <div class="p-my-event__no-event" v-else>
          作成したイベントはありません。
        </div>
      </div>
    </content-inner>
  </div>
</template>

<script>
import axios from 'axios';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import store from '../store';

export default {
  components: {
    Loading
  },
  data: function(){
    return {
      title: '作成イベント一覧',
      explain: '特になし',
      candidateDate: [],
      comment: '',
      eventData: [],
      isLoading: true
    }
  },
  created: function() {
    this.getMyEventData();
  },
  beforeRouteEnter: (to, from, next) => {
    if(store.getters.isSignedIn){
      next();
      axios.post('/check_user_exist',{ //ユーザーページの存在チェック
        twitterId: to.params.twitter_id
      }).then((response) => {
        if(response.data.isUserExist){
          next();
        }else{
          next({path: '/404'});
        }
      }).catch((e) => {
        this.handleErrors({e : e, router : null });
      });
    }else{
      next('/login');
    }
  },
  methods: {
    getMyEventData: function(){ //イベントのデータを取得
      axios.post('/select_my_event_list',{
        twitterId: store.getters.user.twitterId
      }).then(response=> {
        this.eventData = response.data;
        setTimeout(() => {
          this.isLoading = false;
        }, 1000);
      }).catch((e) => {
        this.handleErrors({e : e, router : this.$router});
        setTimeout(() => {
          this.isLoading = false;
        }, 1000);
      });
    }
  }
}
</script>
