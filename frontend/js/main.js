// import global style
import 'styles/app.scss'
// General behaviors
import Vue from 'vue'
import navToggle from '@/behaviors/navToggle'
import showEnvLine from '@/behaviors/showEnvLine'
import search from '@/main-search'

const A17Init = function () {
  navToggle()
  showEnvLine()
}

// User header dropdown
/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
console.log(window[process.env.VUE_APP_NAME])
if (!window[process.env.VUE_APP_NAME]) {
  window[process.env.VUE_APP_NAME] = {}
}
window[process.env.VUE_APP_NAME].vheader = new Vue({ el: '#headerUser' })

// Search
/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
window[process.env.VUE_APP_NAME].vsearch = search
console.log('\x1b[32m', `Made with ${process.env.VUE_APP_NAME} - v${process.env.VUE_APP_VERSION}`)
export default A17Init
