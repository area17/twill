import { createApp } from 'vue'
import store from '@/store'
import Trend from 'vue3trend'

// General shared behaviors
import main from '@/main'
import search from '@/main-search'
import registerCustomComponents from '@/custom-components'
import openMediaLibrary from '@/behaviors/openMediaLibrary'

// Plugins
import A17Config from '@/plugins/A17Config'
import A17Notif from '@/plugins/A17Notif'

// Dashboard
import a17ShortcutCreator from '@/components/dashboard/shortcutCreator.vue'
import A17ActivityFeed from '@/components/dashboard/activityFeed.vue'
import A17StatFeed from '@/components/dashboard/statFeed.vue'
import A17GenericFeed from '@/components/dashboard/genericFeed.vue'

// Store modules
import datatable from '@/store/modules/datatable'
import language from '@/store/modules/language'
import form from '@/store/modules/form'

const app = createApp({
  components: {
    'a17-shortcut-creator': a17ShortcutCreator,
    'a17-activity-feed': A17ActivityFeed,
    'a17-stat-feed': A17StatFeed,
    'a17-feed': A17GenericFeed
  },
  created: function () {
    openMediaLibrary(this)
  }
})

store.registerModule('datatable', datatable)
store.registerModule('language', language)
store.registerModule('form', form)

app.use(store)
app.use(Trend)

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
