<template>
  <div class="p-accordion">
    <button class="p-accordion__trigger" type="button" :class="{'state-open': isOpened}" @click="toggle()">
      <div>{{ title }}</div>
    </button>
    <transition name="p-accordion" @enter="enter" @after-enter="afterEnter" @leave="leave" @after-leave="afterLeave">
      <div class="p-accordion__target" :class="{'state-open': isOpened}" v-if="isOpened">
        <div class="p-accordion__body">
          <div class="p-accordion__about-container p-accordion__subcontainer">
            <h2 class="p-accordion__subtitle">イベント説明・連絡事項</h2>
            <div class="p-accordion__about-comment">{{ explain }}</div>
          </div>
          <div class="p-accordion__schedule-container p-accordion__subcontainer">
            <h2 class="p-accordion__subtitle">候補日程</h2>
            <div class="p-accordion__table-area">
              <table class="p-accordion__table">
                <thead class="p-accordion__table-thead">
                  <tr>
                    <th class="p-accordion__table-title">日程</th>
                    <td class="p-accordion__table-data">◯</td>
                    <td class="p-accordion__table-data">△</td>
                    <td class="p-accordion__table-data">×</td>
                    <td v-for="(item, index) in eachVotes" class="p-accordion__table-data">
                      {{ item.name }}
                    </td>
                  </tr>
                </thead>
                <tbody class="p-accordion__table-body">
                  <tr v-for="(item, index) in candidateDate" v-bind:key="item.id">
                    <th class="p-accordion__table-title">{{ item.date }}</th>
                    <td class="p-accordion__table-data">{{ item.circleCount }}人</td>
                    <td class="p-accordion__table-data">{{ item.triangleCount }}人</td>
                    <td class="p-accordion__table-data">{{ item.crossCount }}人</td>
                    <td v-for="vote in item.vote" class="p-accordion__table-data">
                      {{ vote }}
                    </td>
                  </tr>
                  <tr>
                    <th class="p-accordion__table-title">コメント</th>
                    <td class="p-accordion__table-data"></td>
                    <td class="p-accordion__table-data"></td>
                    <td class="p-accordion__table-data"></td>
                    <td v-for="(item, index) in eachVotes" class="p-accordion__table-data">
                      {{ item.comment }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="p-accordion__url-container p-accordion__subcontainer">
            <h2 class="p-accordion__subtitle">スケジュール入力URL</h2>
            <div class="p-accordion__share-container">
              <p class="p-accordion__url">{{ eventURL }}</p>
              <a class="p-accordion__share-link" :href="shareURL+eventId">
                イベントをDMで共有
              </a>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>


<script>
import axios from 'axios';

function nextFrame(fn){
  window.requestAnimationFrame(() => window.requestAnimationFrame(fn));
}

export default {
  data: function(){
    return{
      isOpened: false,
      explain: '説明はありません',
      candidateDate: [],
      voteCount: [],
      voters: [],
      eachVotes: [],
      attendance: [],
      url: 'http://127.0.0.1:8000/event/',
      eventURL: '',
      shareURL: 'http://127.0.0.1:8000/share/'
    }
  },
  props: {
    title: {type: String, required: true},
    eventId: {type: String, required: true}
  },
  methods: {
    toggle: function(){
      axios.post('/get_event_detail',{
        eventId: this.eventId
      }).then(response => {

        this.initialState();

        if(response.data.explain){
          this.explain = response.data.explain;
        }
        this.voteCount = response.data.vote;
        this.eventURL = this.url+this.eventId;
        this.attendance = response.data.attendance;

        let dateStringArray = response.data.candidateDate.split('\n');

        for(let i = 0; i < dateStringArray.length; i++){
          let obj = {'date': dateStringArray[i],
                     'circleCount': this.voteCount[i][0],
                     'triangleCount': this.voteCount[i][1],
                     'crossCount': this.voteCount[i][2],
                     'vote':this.attendance[i]
                   };
          this.candidateDate.push(obj);
        }

        for(let i = 0; i < response.data.voters.length; i++){
          let obj = {'name': response.data.voters[i],
                     'comment': response.data.comments[i]};
          this.eachVotes.push(obj);
        }
        this.isOpened = !this.isOpened
      }).catch((e) => {
        this.handleErrors({e : e, router : this.$router});
      });

    },
    initialState: function(){
      this.explain = '説明はありません';
      this.candidateDate = [];
      this.voteCount = [];
      this.eventURL = '';
      this.eachVotes = [];
    },
    formatDate: function(date, format){
      format = format.replace(/YYYY/, date.getFullYear());
      format = format.replace(/MM/, date.getMonth()+1);
      format = format.replace(/DD/, date.getDate());
      return format;
    },
    enter: function(el){
      el.style.overflow = 'hidden';
      el.style.height = '0';
      nextFrame(() => {
        el.style.height = el.scrollHeight+'px';
      });
    },
    leave: function(el){
      el.style.overflow = 'hidden';
      el.style.height = el.scrollHeight+'px';
      nextFrame(() => {
        el.style.height = '0';
      })
    },
    afterEnter: function(el){
      el.style.height = '';
      el.style.overflow = '';
    },
    afterLeave: function(el){
      el.style.height = '';
      el.style.overflow = '';
    }
  }
}
</script>
