// import global style
import 'styles/app.scss'
// General behaviors
import Vue from 'vue'
import navToggle from '@/behaviors/navToggle'
import showEnvLine from '@/behaviors/showEnvLine'
import logoutButton from '@/behaviors/logoutButton'
import search from '@/main-search'
import merge from 'lodash/merge'
import BlockMixin from '@/mixins/block'
// Alpine js
import Alpine from 'alpinejs'
import mask from '@alpinejs/mask'

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

// Blocks
const registerBlockComponent = (name, component) => {
  return !Vue.options.components[name]
    ? Vue.component(name, component)
    : false
}

if (typeof window[process.env.VUE_APP_NAME].TWILL_BLOCKS_COMPONENTS !== 'undefined') {
  window[process.env.VUE_APP_NAME].TWILL_BLOCKS_COMPONENTS.map(componentName => {
    return registerBlockComponent(componentName, {
      template: '#' + componentName,
      mixins: [BlockMixin]
    })
  })
}

// Custom components
const extractComponentNameFromContextKey = (contextKey) => `a17-${contextKey.match(/\w+/)[0].replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, '-').toLowerCase()}`

const importedCustomBlocks = require.context('@/components/blocks/customs/', false, /\.(js|vue)$/i)
importedCustomBlocks.keys().map(block => {
  const componentName = extractComponentNameFromContextKey(block.replace(/customs\//, ''))
  return registerBlockComponent(componentName, importedCustomBlocks(block).default)
})

const importedTwillBlocks = require.context('@/components/blocks/', false, /\.(js|vue)$/i)
importedTwillBlocks.keys().map(block => {
  const componentName = extractComponentNameFromContextKey(block)
  return registerBlockComponent(componentName, importedTwillBlocks(block).default)
})

// Custom form components
const importedComponents = require.context(process.env.VUE_APP_CUSTOM_COMPONENTS_PATH, true, /\.(js|vue)$/i)
importedComponents.keys().map(block => {
  // eslint-disable-next-line
  const componentName = extractComponentNameFromContextKey(block)
  return Vue.component(componentName, importedComponents(block).default)
})

// Vendor form components
const importedVendorComponents = require.context('@/components/customs-vendor/', true, /\.(js|vue)$/i)
importedVendorComponents.keys().map(block => {
  const componentName = extractComponentNameFromContextKey(block)
  return Vue.component(componentName, importedVendorComponents(block).default)
})


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
