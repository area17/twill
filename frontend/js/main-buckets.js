// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import store from '@/store'

// General behaviors
import main from '@/main'

// Plugins
import A17Config from '@/plugins/A17Config'

// Buckets
import a17Buckets from '@/components/buckets/Bucket.vue'

// configuration
Vue.use(A17Config)

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
Window.vm = new Vue({
  store, // inject store to all children
  el: '#app',
  components: {
    'a17-buckets': a17Buckets
  }
})

// DOM Ready general actions
document.addEventListener('DOMContentLoaded', main)
