<template>
  <aside class="p-sidebar">
    <div class="p-sidebar__content">
      <div class="p-sidebar__profile">
        <div class="p-sidebar__icon-box">
          <img class="p-sidebar__icon" :src="icon">
        </div>
        <div class="p-sidebar__name">
          <span class="p-sidebar__display-name">{{ displayName }}</span>
          <span class="p-sidebar__account-name">{{ accountName }}</span>
        </div>
      </div>
    </div>
  </aside>
</template>

<script>

import axios from 'axios';

export default {
  name: 'Sidebar',
  data: function() {
    return {
      icon: '',
      displayName: '',
      accountName: ''
    }
  },
  mounted: function() {
    this.getAccountData();
  },
  methods: {
    getAccountData: function(){
      axios.get('/get_account_data').then(response=> {
        this.icon = response.data.photoUrl;
        this.displayName = response.data.displayName;
        this.accountName = response.data.accountName;
      }).catch((e) => {
        this.handleErrors({e : e, router : this.$router});
      });
    }
  }
}
</script>
