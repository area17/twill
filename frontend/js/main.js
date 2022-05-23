// import global style
import 'styles/app.scss'
// General behaviors
import Vue from 'vue'
import navToggle from '@/behaviors/navToggle'
import showEnvLine from '@/behaviors/showEnvLine'
import logoutButton from '@/behaviors/logoutButton'
import search from '@/main-search'
import merge from 'lodash/merge'

const A17Init = function () {
  navToggle()
  showEnvLine()
  logoutButton()
}

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
console.log('\x1b[32m', `TEMP Made with ${process.env.VUE_APP_NAME} - v${window[process.env.VUE_APP_NAME].version}`)

merge(
  window[process.env.VUE_APP_NAME].STORE,
  window.STORE
)

export default A17Init
