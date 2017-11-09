// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import store from '@/store'

// Plugins
import A17Config from '@/plugins/A17Config'

// Dashboard
import a17ShortcutCreator from '@/components/dashboard/shortcutCreator.vue'
import A17ActivityFeed from '@/components/dashboard/activityFeed.vue'
import A17StatFeed from '@/components/dashboard/statFeed.vue'
import A17PopularFeed from '@/components/dashboard/popularFeed.vue'

// configuration
Vue.use(A17Config)

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
Window.vm = new Vue({
  store, // inject store to all children
  el: '#app',
  components: {
    'a17-shortcut-creator': a17ShortcutCreator,
    'a17-activity-feed': A17ActivityFeed,
    'a17-stat-feed': A17StatFeed,
    'a17-popular-feed': A17PopularFeed
  }
})
