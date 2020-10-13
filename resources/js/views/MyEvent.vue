<template>
  <content-inner :title='title'>
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
</template>

<script>
import axios from 'axios';

export default {
  data: function(){
    return {
      title: '作成イベント一覧',
      explain: '特になし',
      candidateDate: [],
      comment: '',
      eventData: []
    }
  },
  props: {
    twitter_id: String
  },
  mounted: function() {
    this.getMyEventData();
  },
  beforeRouteEnter: (to, from, next) => {
    axios.get('/auth_check').then(response=> { //認証チェック
      if(response.data.authStatus){
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
    });
  },
  methods: {
    getMyEventData: function(){ //イベントのデータを取得
      axios.post('/get_my_event_data',{
        twitterId: this.twitter_id
      }).then(response=> {
        this.eventData = response.data;
      }).catch((e) => {
        this.handleErrors({e : e, router : this.$router});
      });
    }
  }
}
</script>
