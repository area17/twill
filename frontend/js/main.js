// import global style
import 'styles/app.scss'
// General behaviors
import { createApp } from "vue"
import navToggle from '@/behaviors/navToggle'
import showEnvLine from '@/behaviors/showEnvLine'
import logoutButton from '@/behaviors/logoutButton'
import search from '@/main-search'
import merge from 'lodash/merge'
// Alpine js
import Alpine from 'alpinejs'
import mask from '@alpinejs/mask'
// Plugins
import A17Config from "@/plugins/A17Config";

const A17Init = function () {
  navToggle()
  showEnvLine()
  logoutButton()
}

if (module && module.hot) {
  /* eslint-disable */
  __webpack_public_path__ = window.hmr_url + '/'
  /* eslint-enable */
}

// Alpine js
Alpine.plugin(mask)
window.Alpine = Alpine

Alpine.start()

// User header dropdown
/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
if (!window[process.env.VUE_APP_NAME]) {
  window[process.env.VUE_APP_NAME] = {}
}

const app = createApp({})

// configuration
app.use(A17Config)

app.mount('#headerUser')

window[process.env.VUE_APP_NAME].vheader = app

// Search
/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
window[process.env.VUE_APP_NAME].vsearch = search
/* eslint-disable no-console */
console.log('\x1b[32m', `Made with ${process.env.VUE_APP_NAME} - v${window[process.env.VUE_APP_NAME].version}`)

merge(
  window[process.env.VUE_APP_NAME].STORE,
  window.STORE
)

export default A17Init
