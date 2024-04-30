import { createApp } from 'vue'
import store from '@/store'

// General shared behaviors
import main from '@/main'
import search from '@/main-search'
import registerCustomComponents from '@/custom-components'
import openMediaLibrary from '@/behaviors/openMediaLibrary'

// Buckets
import a17Buckets from '@/components/buckets/Bucket.vue'

// Plugins
import A17Config from '@/plugins/A17Config'
import A17Notif from '@/plugins/A17Notif'

// Store modules
import buckets from '@/store/modules/buckets'
import language from '@/store/modules/language'
import form from '@/store/modules/form'

store.registerModule('buckets', buckets)
store.registerModule('language', language)
store.registerModule('form', form)

const app = createApp({
  components: {
    'a17-buckets': a17Buckets
  },
  created: function () {
    openMediaLibrary(this)
  }
})

app.use(store)
// configuration
app.use(A17Config)
app.use(A17Notif)

registerCustomComponents(app)

app.mount('#app')
/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
window[process.env.VUE_APP_NAME].vm = window.vm = app

// DOM Ready general actions
document.addEventListener('DOMContentLoaded', main)
