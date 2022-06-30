// import global style
// import 'styles/app.scss'
import "../scss/app.scss";
// General behaviors
import Vue from 'vue'
import navToggle from '@/behaviors/navToggle'
import showEnvLine from '@/behaviors/showEnvLine'
import logoutButton from '@/behaviors/logoutButton'
import search from '@/main-search'
import merge from 'lodash/merge'
// Alpine js
import Alpine from 'alpinejs'
import mask from '@alpinejs/mask'

const A17Init = function () {
  navToggle()
  showEnvLine()
  logoutButton()
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
window[process.env.VUE_APP_NAME].vheader = new Vue({ el: '#headerUser' })

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
