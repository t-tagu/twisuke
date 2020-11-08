<template>
  <div class="p-accordion">
    <loading :active.sync="isFetchingEventData"
          :can-cancel="false"
          :is-full-page="true"></loading>
    <button class="p-accordion__trigger" type="button" :class="{'state-open': isAccordionOpened}" @click="toggle()">
      <div>{{ title }}</div>
    </button>
    <transition name="p-accordion" @enter="enter" @after-enter="afterEnter" @leave="leave" @after-leave="afterLeave">
      <div class="p-accordion__target" :class="{'state-open': isAccordionOpened}" v-if="isAccordionOpened">
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
                    <td v-for="(item, index) in eachVotes" class="p-accordion__table-data p-accordion__table-data--minifont">
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
              <a class="p-accordion__share-link" :href="eventShareURL">
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
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import config from '../const';

const LOADING_DELAY_TIME = 1000;

const nextFrame = (fn) => {
  window.requestAnimationFrame(() => window.requestAnimationFrame(fn));
};

export default {
  components: {
    Loading
  },
  data(){
    return{
      isAccordionOpened: false,
      explain: '説明はありません',
      candidateDate: [],
      voteCount: [],
      voters: [],
      eachVotes: [],
      attendance: [],
      eventURL: '',
      isFetchingEventData: false
    }
  },
  props: {
    title: {type: String, required: true},
    eventId: {type: String, required: true}
  },
  computed: {
    eventShareURL(){
      return config.BASE_URL+'/share/'+this.eventId;
    }
  },
  methods: {
    toggle(){

      if(!this.isAccordionOpened){ //開く時

        const transitionFrom = { fromMyEventPage : 2 };

        this.isFetchingEventData = true;
        axios.post('/select_my_event_detail',{
          eventId: this.eventId,
          transitionFrom: transitionFrom.fromMyEventPage
        }).then((response) => {

          this.initialState();

          if(response.data.explain){
            this.explain = response.data.explain;
          }
          this.voteCount = response.data.vote;
          this.eventURL = config.BASE_URL+'/event/'+this.eventId;
          this.attendance = response.data.attendance;

          let dateStringArray = response.data.candidateDate;

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

          setTimeout(() => {
            this.isFetchingEventData = false;
            this.isAccordionOpened = !this.isAccordionOpened
          }, LOADING_DELAY_TIME);

        }).catch((e) => {
          this.handleErrors({e : e, router : this.$router});
          setTimeout(() => {
            this.isFetchingEventData = false;
          }, LOADING_DELAY_TIME);
        });

      }else{  //閉じるとき
        this.isAccordionOpened = !this.isAccordionOpened
      }

    },
    initialState(){
      this.explain = '説明はありません';
      this.candidateDate = [];
      this.voteCount = [];
      this.eventURL = '';
      this.eachVotes = [];
    },
    enter(el){
      el.style.overflow = 'hidden';
      el.style.height = '0';
      nextFrame(() => {
        el.style.height = el.scrollHeight+'px';
      });
    },
    leave(el){
      el.style.overflow = 'hidden';
      el.style.height = el.scrollHeight+'px';
      nextFrame(() => {
        el.style.height = '0';
      })
    },
    afterEnter(el){
      el.style.height = '';
      el.style.overflow = '';
    },
    afterLeave(el){
      el.style.height = '';
      el.style.overflow = '';
    }
  }
}
</script>
