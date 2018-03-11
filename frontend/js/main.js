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
Window.vheader = new Vue({ el: '#headerUser' })

// Search
/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
Window.vsearch = search

export default A17Init
