// Header menu
import Vue from 'vue'
import a17Dropdown from '@/components/Dropdown.vue'

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
Window.vheader = new Vue({
  el: '#headerUser',
  components: {
    'a17-dropdown': a17Dropdown
  }
})

// General behaviors
import navToggle from '@/behaviors/navToggle'
import showEnvLine from '@/behaviors/showEnvLine'

const A17Init = function () {
  navToggle()
  showEnvLine()
}

export default A17Init
