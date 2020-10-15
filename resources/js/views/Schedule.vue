<template>
  <content-inner :title='title'>
    <div class="p-schedule__container">
      <form class="p-schedule__form" v-on:submit.prevent="makeEvent">
        <div class="p-schedule__title-container p-schedule__subcontainer">
          <h2 class="p-schedule__subtitle">イベント名</h2>
          <input class="p-schedule__name" v-model="name" :maxlength="nameMaxLength" placeholder="イベント名を入力(必須40文字以内)">
          <div class="p-schedule__name-count-box">
            <span class="p-schedule__name-count">{{ nameCount }}/{{ nameMaxLength }}</span>
          </div>
        </div>
        <div class="p-schedule__about-container p-schedule__subcontainer">
          <h2 class="p-schedule__subtitle">イベント説明・連絡事項</h2>
          <textarea class="p-schedule__explain" placeholder="イベントの説明や連絡事項を入力(任意500文字以内)" :maxlength="explainMaxLength" rows="5" v-model="explain">{{ explain }}</textarea>
          <div class="p-schedule__explain-count-box">
            <span class="p-schedule__explain-count">{{ explainCount }}/{{ explainMaxLength }}</span>
          </div>
        </div>
        <div class="p-schedule__calendar-container p-schedule__subcontainer">
          <h2 class="p-schedule__subtitle">候補日程</h2>
          <div class="p-schedule__format">
            ※1行に1つずつ候補日程と日時を以下の例のように改行で区切りながら記入して下さい。<br>
            記入例:<br>
            2021/07/22 19:00<br>
            2021/07/23 17:00<br>
            2021/07/24 20:00<br>
          </div>
          <div class="p-schedule__calendar-area">
            <div class="p-schedule__candidate-container">
              <textarea class="p-schedule__candidate-schedule" placeholder="イベントの広報日程を入力" v-model="candidateDate">{{ candidateDate }}</textarea>
              <button class="p-schedule__calendar-btn" type="button" v-on:click="showAndHide()"><i class="fas fa-calendar-alt"></i></button>
            </div>
            <datepicker class="p-schedule__calendar" :format="datePickerFormat" :inline="inline" :language="ja" :disabled-dates="disabledDates"
                        @input="selected()" v-model="selectedDate" v-show="isCalendarShow"></datepicker>
          </div>
        </div>
        <input class="c-button p-schedule__button" type="submit" value="イベントを作成する">
      </form>
    </div>
  </content-inner>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import Datepicker from 'vuejs-datepicker';
import {ja} from 'vuejs-datepicker/dist/locale';

let ENTER_EVENT_NAME = 'イベントの名前を入力して下さい。';

export default {
  components: {
    Datepicker
  },
  computed: {
    nameCount: function() {
      return this.name.length;
    },
    explainCount: function() {
      return this.explain.length;
    },
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
  created(){
    window.addEventListener('resize', this.handleResize);
    this.handleResize()
  },
  destroyed(){
    window.removeEventListener('resize', this.handleResize);
  },
  data: function(){
    return {
      title: 'イベント作成',
      name: '',
      explain: '',
      nameMaxLength: 40,
      explainMaxLength: 500,
      dates: [],
      candidateDate:'',
      mode: 'single',
      formats: {
        input: ['YYYY-MM-DD']
      },
      selectedDate: null,
      datePickerFormat: 'yyyy-MM-dd',
      inline: true,
      ja: ja,
      disabledDates: {
        to: new Date()
      },
      isCalendarShow: false
    }
  },
  methods: {
    makeEvent: function(){ //イベントのデータをDBに登録

      let eventName = this.name;
      let explain = this.explain;

      if(!eventName){
        alert(ENTER_EVENT_NAME);
        return;
      }

      if(eventName.length > this.nameMaxLength){
        alert('イベント名は'+this.nameMaxLength+'文字までです。');
        return;
      }

      if(explain.length > this.explainMaxLength){
        alert('説明は'+this.explainMaxLength+'文字までです。');
        return;
      }

      if(!((this.candidateDate.trim()).length)){
        alert('候補日程を入力して下さい。');
        return;
      }

      let formData = new FormData();
      formData.append('name',eventName);
      formData.append('explain',this.explain);
      formData.append('candidateDate',JSON.stringify((this.candidateDate.trim()).split(/\n/)));

      axios.post('/make_event',formData,{
      }).then(response => {
        this.$router.push({name:'share', params: { event_id: response.data.eventId }});
      }).catch((e) => {
        this.handleErrors({e : e, router : this.$router});
      });
    },
    formatDate: function(value){ //日付けのフォーマットを揃える
      return moment(value).format('YYYY/MM/DD 19:00');
    },
    selected: function(){
      this.candidateDate += this.formatDate(this.selectedDate)+'\n';
    },
    showAndHide: function(){
      this.isCalendarShow = !this.isCalendarShow;
    },
    handleResize: function(){
      if(window.innerWidth >= 600){
        this.isCalendarShow = true;
      }
    }
  }
}
</script>
