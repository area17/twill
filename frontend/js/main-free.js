import { createApp } from 'vue'
import store from '@/store'

// General shared behaviors
import main from '@/main'
import search from '@/main-search'
import registerCustomComponents from '@/custom-components'
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

store.registerModule('form', form)
store.registerModule('browser', browser)

const app = createApp({
  components: {
    'a17-fieldset': a17Fieldset,
    'a17-browser': a17Browser
  },
  created: function () {
    openMediaLibrary()
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
