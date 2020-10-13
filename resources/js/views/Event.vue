<template>
  <content-inner :title='title'>
    <div class="p-event__container">
      <flash-message :value='flashMessage' :isShow="isFlashShow"></flash-message>
      <form class="p-event__form" v-on:submit.prevent="enterSchedule">
        <div class="p-event__title-container p-event__subcontainer">
          <h2 class="p-event__subtitle">イベント名</h2>
          <div class="p-event__name">{{ eventName }}</div>
        </div>
        <div class="p-event__about-container p-event__subcontainer">
          <h2 class="p-event__subtitle">イベント説明・連絡事項</h2>
          <div class="p-event__about-comment">{{ explain }}</div>
        </div>
        <div class="p-event__secretary-container p-event__subcontainer">
          <h2 class="p-event__subtitle">幹事</h2>
          <a class="p-event__secretary-link" :href="secretaryURL">
            <div class="p-event__secretary-content">
              <div class="p-event__icon-box">
                <img class="p-event__icon" :src="image">
              </div>
              <span class="p-event__secretary-name u-block">
                {{ name }}@{{ account }}
              </span>
            </div>
          </a>
        </div>
        <div class="p-event__schedule-container p-event__subcontainer">
          <h2 class="p-event__subtitle">候補日程</h2>
          <div class="p-event__table-area">
            <table class="p-event__table">
              <thead class="p-event__table-thead">
                <tr>
                  <th class="p-event__table-title">日程</th>
                  <td class="p-event__table-data">◯</td>
                  <td class="p-event__table-data">△</td>
                  <td class="p-event__table-data">×</td>
                  <td v-for="(item, index) in eachVotes" class="p-event__table-data">
                    {{ item.name }}
                  </td>
                </tr>
              </thead>
              <tbody class="p-event__table-body">
                <tr v-for="(item, index) in candidateDate" v-bind:key="item.id">
                  <th class="p-event__table-title">{{ item.date }}</th>
                  <td class="p-event__table-data">{{ item.circleCount }}人</td>
                  <td class="p-event__table-data">{{ item.triangleCount }}人</td>
                  <td class="p-event__table-data">{{ item.crossCount }}人</td>
                  <td v-for="vote in item.vote" class="p-event__table-data">
                    {{ vote }}
                  </td>
                </tr>
                <tr>
                  <th class="p-event__table-title">コメント</th>
                  <td class="p-event__table-data"></td>
                  <td class="p-event__table-data"></td>
                  <td class="p-event__table-data"></td>
                  <td v-for="(item, index) in eachVotes" class="p-accordion__table-data">
                    {{ item.comment }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="p-event__calendar-container p-event__subcontainer">
          <h2 class="p-event__subtitle">出欠入力</h2>
          <div class="p-enter">
            <ul class="p-enter__list" v-if="candidateDate.length">
              <li class="p-enter__list-item" v-for="(item, index) in candidateDate" v-bind:key="item.id">
                <div class="p-enter__list-item-inner">
                  <div class="p-enter__list-item-date">{{ item.date }}</div>
                  <div class="p-enter__list-item-select">
                    <div class="p-enter__list-item-btn-box">
                      <input class="p-enter__list-item-circle p-enter__list-item-btn" type="radio" v-on:change="onSubmissionDayChange" :name="item.day" :id="item.radio1" value="1">
                      <label class="p-enter__list-item-label" :for="item.radio1"><i class="icon far fa-circle p-enter__list-item-icon"></i></label>
                    </div>
                    <div class="p-enter__list-item-btn-box">
                      <input class="p-enter__list-item-triangle p-enter__list-item-btn" type="radio" v-on:change="onSubmissionDayChange" :name="item.day" :id="item.radio2" value="2">
                      <label class="p-enter__list-item-label" :for="item.radio2"><i class="icon far fa-play-circle p-enter__list-item-icon fa-rotate-270"></i></label>
                    </div>
                    <div class="p-enter__list-item-btn-box">
                      <input class="p-enter__list-item-cross p-enter__list-item-btn" type="radio" v-on:change="onSubmissionDayChange" :name="item.day" :id="item.radio3" value="3" checked>
                      <label class="p-enter__list-item-label" :for="item.radio3"><i class="icon far fa-times-circle p-enter__list-item-icon"></i></label>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="p-event__comment-area">
            <h2 class="p-event__subtitle">コメント</h2>
            <textarea class="p-event__comment" placeholder="例)20:00から参加します！" v-model="comment">{{ comment }}</textarea>
          </div>
        </div>
        <input class="c-button p-event__button" type="submit" value="日程を入力する">
      </form>
    </div>
  </content-inner>
</template>

<script>
import axios from 'axios';

let NOT_FOUND = 404; //ページが存在しない場合

export default {
  data: function(){
    return{
      title: 'スケジュール入力',
      eventName: '',
      explain: '特になし',
      name: '',
      account: '',
      image: '',
      candidateDate: [],
      comment: '',
      submissionDate: [],
      crossDay: 3,
      voteCount: [],
      voters: [],
      eachVotes: [],
      attendance: [],
      flashMessage: '',
      isFlashShow: false
    }
  },
  props: {
    event_id: String
  },
  computed: {
    secretaryURL :function(){
      return 'https://twitter.com/'+this.account;
    }
  },
  mounted: function() {
    this.getEventData();
  },
  beforeRouteEnter: (to, from, next) => {
    axios.get('/auth_check').then(response=> { //認証チェック
      if(response.data.authStatus){
        next();

        axios.post('/check_event_exist',{ //イベントの存在チェック
          eventId: to.params.event_id
        }).then((response) => {
          if(response.data.isEventExist){
            next();
          }else{
            next({path:'/404'});
          }
        }).catch((e) => {
          this.handleErrors({e : e, router : null});
        });

      }else{ //認証できなかった場合
        next('/login');
      }
    });
  },
  methods: {
    getEventData: function(){ //イベントのデータを取得
      axios.post('/get_event_data',{
        eventId: this.event_id
      }).then(response=> {

        this.eventName = response.data.eventName;
        if(response.data.explain){
          this.explain = response.data.explain;
        }
        this.name = response.data.secretaryName;
        this.account = response.data.secretaryAccount;
        this.image = response.data.profileImage;
        this.voteCount = response.data.vote;
        this.attendance = response.data.attendance;

        let dateStringArray = response.data.candidateDate.split('\n');
        console.log(dateStringArray);

        for(let i = 0; i < dateStringArray.length; i++){
          let obj = {'date': dateStringArray[i],
                     'day': i,
                     'radio1': 'radio'+i+'-1',
                     'radio2': 'radio'+i+'-2',
                     'radio3': 'radio'+i+'-3',
                     'circleCount': this.voteCount[i][0],
                     'triangleCount': this.voteCount[i][1],
                     'crossCount': this.voteCount[i][2],
                     'vote': this.attendance[i]
                    };
          this.candidateDate.push(obj);
          this.submissionDate.push(this.crossDay); //提出日の初期値は行けない日に設定
        }

        for(let i = 0; i < response.data.voters.length; i++){
          let obj = {'name': response.data.voters[i],
                     'comment': response.data.comments[i]};
          this.eachVotes.push(obj);
        }

      }).catch((e) => {
        this.handleErrors({e : e, router : this.$router});
      });

    },
    formatDate: function(date, format){
      format = format.replace(/YYYY/, date.getFullYear());
      format = format.replace(/MM/, date.getMonth()+1);
      format = format.replace(/DD/, date.getDate());
      return format;
    },
    enterSchedule: function(){

      let formData = new FormData();
      formData.append('eventId',this.event_id);
      formData.append('submissionDate',this.submissionDate.join(','));
      formData.append('comment',this.comment);

      axios.post('/enter_schedule',formData,{
      }).then(response => {
        this.showFlashMessage('スケジュールを入力しました！');
      }).catch((e) => {
        this.handleErrors({e : e, router : this.$router});
      });

    },
    onSubmissionDayChange: function(event){ //提出日の変更
      this.submissionDate.splice(event.target.name, 1, Number(event.target.value));
      console.log(this.submissionDate);
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
