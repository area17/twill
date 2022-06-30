import Vue from 'vue'
import store from '@/store'

// General shared behaviors
import main from '@/main'
import search from '@/main-search'
import openMediaLibrary from '@/behaviors/openMediaLibrary'

// Plugins
import A17Config from '@/plugins/A17Config'
import A17Notif from '@/plugins/A17Notif'

// Store Modules
import form from '@/store/modules/form'
import browser from '@/store/modules/browser'

// Page Components
import a17Fieldset from '@/components/Fieldset.vue'
import a17Browser from '@/components/Browser.vue'

// configuration
Vue.use(A17Config)
Vue.use(A17Notif)

store.registerModule('form', form)
store.registerModule('browser', browser)

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
window[import.meta.env.VITE_APP_NAME].vm = window.vm = new Vue({
  store, // inject store to all children
  el: '#app',
  components: {
    'a17-fieldset': a17Fieldset,
    'a17-browser': a17Browser
  },
  created: function () {
    openMediaLibrary()
  }
})

// DOM Ready general actions
document.addEventListener('DOMContentLoaded', main)
