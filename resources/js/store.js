import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  strict: false,
  state: {
    user: {},
    status: false
  },
  mutations: {
    setUserLoginStatus(state, status){
      state.status = status;
    },
    setUserInfo(state, user){
      state.user = user
    }
  },
  getters: {
    user(state){
      return state.user;
    },
    isSignedIn(state){
      return state.status;
    }
  }
});
