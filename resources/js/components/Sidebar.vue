<template>
  <aside class="p-sidebar">
    <div class="p-sidebar__content">
      <div class="p-sidebar__profile">
        <div class="p-sidebar__icon-box">
          <img class="p-sidebar__icon" :src="photoURL">
        </div>
        <div class="p-sidebar__name">
          <span class="p-sidebar__display-name">{{ displayName }}</span>
        </div>
      </div>
    </div>
  </aside>
</template>

<script>

import axios from 'axios';
import store from '../store';

export default {
  name: 'Sidebar',
  computed: {
    displayName: function() {
      return store.getters.user.displayName;
    },
    photoURL: function() {
      return store.getters.user.photoURL;
    },
  }
}
</script>
