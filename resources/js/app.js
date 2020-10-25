/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import 'vue-select/dist/vue-select.css';

window.Vue = require('vue');

import './bootstrap'
import Vue from 'vue/dist/vue.esm.js';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import Top from './views/Top.vue';
import Login from './views/Login.vue';
import Member from './views/Member.vue';
import Schedule from './views/Schedule.vue';
import Share from './views/Share.vue';
import Event from './views/Event.vue';
import MyEvent from './views/MyEvent.vue';
import NotFound from './views/NotFound.vue';
import VCalendar from 'v-calendar';
import VSelect from 'vue-select';
import VueClipboard from 'vue-clipboard2';
import VueSmoothScroll from 'vue-smooth-scroll';
import util from './util';
import store from './store';

Vue.component('sidebar', require('./components/Sidebar.vue').default);
Vue.component('header-navigation', require('./components/HeaderNavigation.vue').default);
Vue.component('content-inner', require('./components/ContentInner.vue').default);
Vue.component('accordion', require('./components/Accordion.vue').default);
Vue.component('top-header', require('./components/TopHeader.vue').default);
Vue.component('flash-message', require('./components/FlashMessage.vue').default);
Vue.component('trigger-menu', require('./components/TriggerMenu.vue').default);
Vue.component('top-menu-trigger', require('./components/TopMenuTrigger.vue').default);
Vue.component('top-trigger-menu', require('./components/TopTriggerMenu.vue').default);
Vue.component('menu-trigger', require('./components/MenuTrigger.vue').default);
Vue.component('v-select', VSelect);

Vue.mixin(util);
Vue.use(Vuex);
Vue.use(VueRouter);
Vue.use(VCalendar);
Vue.use(VueClipboard);
Vue.use(VueSmoothScroll);

const routes = [
  { path: '/', name: 'top', component: Top},
  { path: '/login', name: 'login', component: Login},
  { path: '/', component: Member,
    children: [
      { path: 'schedule',
        name: 'schedule',
        component: Schedule
      },
      { path: 'share/:event_id',
        name: 'share',
        component: Share,
        props: true
      },
      { path: 'event/:event_id',
        name: 'event',
        component: Event,
        props: true
      },
      { path: 'my_event/:twitter_id',
        name: 'my_event',
        component: MyEvent,
        props: true
      }
    ]
  },
  { path: '*', name: 'not_found', component: NotFound}
];

export const eventBus = new Vue();

const router = new VueRouter({
  mode: 'history',
  routes
});

import Firebase from './firebase';
import firebase from "firebase/app";
import "firebase/auth";
Firebase.init();

firebase.auth().onAuthStateChanged(user => {

  var userInfo = {};
  var isSignedIn = false;
  if(user){
    userInfo = {
        'displayName': user.displayName,
        'photoURL': user.photoURL,
        'twitterId': user.providerData[0].uid
    }
    isSignedIn = true;
  }
  store.commit('setUserInfo', userInfo);
  store.commit('setUserLoginStatus', isSignedIn);

  const app = new Vue({
    router,
    store
  }).$mount('#app');

});
