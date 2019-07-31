import Vue from 'vue'
import store from '@/store'

// General shared behaviors
import main from '@/main'

// Plugins
import A17Config from '@/plugins/A17Config'
import A17Notif from '@/plugins/A17Notif'

// configuration
Vue.use(A17Config)
Vue.use(A17Notif)

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
window.vm = new Vue({
  store, // inject store to all children
  el: '#app',
  components: { },
})

// DOM Ready general actions
document.addEventListener('DOMContentLoaded', main)
