import Vue from 'vue'
import { mapState, mapGetters } from 'vuex'
import store from '@/store'
import { FORM } from '@/store/mutations'
import ACTIONS from '@/store/actions'
import { FORM_MUTATIONS_TO_SUBSCRIBE } from '@/store/mutations/subscribers'

// General shared behaviors
import main from '@/main'
import search from '@/main-search'
import openMediaLibrary from '@/behaviors/openMediaLibrary'

// Page Components
import a17StickyNav from '@/components/StickyNav.vue'
import a17TitleEditor from '@/components/TitleEditor.vue'
import a17Langswitcher from '@/components/LangSwitcher.vue'
import a17Fieldset from '@/components/Fieldset.vue'
import a17Publisher from '@/components/Publisher.vue'
import a17PageNav from '@/components/PageNav.vue'
import a17Blocks from '@/components/blocks/Blocks.vue'
import a17Repeater from '@/components/Repeater.vue'
import a17LocationField from '@/components/LocationField.vue'
import a17ConnectorField from '@/components/ConnectorField.vue'

// Browser
import a17Browser from '@/components/Browser.vue'

// Overlay Previewer
import a17Overlay from '@/components/Overlay.vue'
import a17Previewer from '@/components/Previewer.vue'

// Overlay Editor
import a17Editor from '@/components/Editor.vue'

// Plugins
import A17Config from '@/plugins/A17Config'
import A17Notif from '@/plugins/A17Notif'

// Loader
import a17Spinner from '@/components/Spinner.vue'

// Add attributes
import a17ModalAdd from '@/components/modals/ModalAdd.vue'

// Store Modules
import form from '@/store/modules/form'
import publication from '@/store/modules/publication'
import blocks from '@/store/modules/blocks'
import language from '@/store/modules/language'
import revision from '@/store/modules/revision'
import browser from '@/store/modules/browser'
import repeaters from '@/store/modules/repeaters'
import parents from '@/store/modules/parents'
import attributes from '@/store/modules/attributes'

// mixins
import formatPermalink from '@/mixins/formatPermalink'
import editorMixin from '@/mixins/editor.js'
import BlockMixin from '@/mixins/block'

// configuration
Vue.use(A17Config)
Vue.use(A17Notif)

store.registerModule('form', form)
store.registerModule('publication', publication)
store.registerModule('blocks', blocks)
store.registerModule('language', language)
store.registerModule('revision', revision)
store.registerModule('browser', browser)
store.registerModule('repeaters', repeaters)
store.registerModule('parents', parents)
store.registerModule('attributes', attributes)

// Form components
Vue.component('a17-fieldset', a17Fieldset)
Vue.component('a17-publisher', a17Publisher)
Vue.component('a17-title-editor', a17TitleEditor)
Vue.component('a17-blocks', a17Blocks)
Vue.component('a17-page-nav', a17PageNav)
Vue.component('a17-langswitcher', a17Langswitcher)
Vue.component('a17-sticky-nav', a17StickyNav)
Vue.component('a17-spinner', a17Spinner)

// Browser
Vue.component('a17-repeater', a17Repeater)
Vue.component('a17-browser', a17Browser)

// Form : connector fields
Vue.component('a17-connectorfield', a17ConnectorField)

// Form: map component
Vue.component('a17-locationfield', a17LocationField)

// Preview
Vue.component('a17-overlay', a17Overlay)
Vue.component('a17-previewer', a17Previewer)

// Editor
Vue.component('a17-editor', a17Editor)

// Add attributes
Vue.component('a17-modal-add', a17ModalAdd)

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
const importedComponents = require.context('@/components/customs/', true, /\.(js|vue)$/i)
importedComponents.keys().map(block => {
  const componentName = extractComponentNameFromContextKey(block)
  return Vue.component(componentName, importedComponents(block).default)
})

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
window[process.env.VUE_APP_NAME].vm = window.vm = new Vue({
  store, // inject store to all children
  el: '#app',
  mixins: [formatPermalink, editorMixin],
  data: function () {
    return {
      unSubscribe: function () {
        return null
      },
      isFormUpdated: false
    }
  },
  computed: {
    ...mapState({
      loading: state => state.form.loading,
      editor: state => state.blocks.editor,
      isCustom: state => state.form.isCustom
    }),
    ...mapGetters([
      'getSaveType',
      'isEnabledSubmitOption'
    ])
  },
  methods: {
    submitForm: function (event) {
      if (!this.loading) {
        this.isFormUpdated = false
        this.$store.commit(FORM.UPDATE_FORM_LOADING, true)
        this.unSubscribe()

        // let's wait for the loading state to be properly deployed (used to save content of wysiwyg fields)
        this.$nextTick(() => {
          const saveType = this.getSaveType || document.activeElement.name

          // isEnabledSubmitOption is an extra check to test is the form can be submit by making sure the saveType is not disabled
          if (this.isEnabledSubmitOption(saveType)) {
            this.$store.dispatch(ACTIONS.SAVE_FORM, saveType).then(() => {
              this.mutationsSubscribe()
            })
          } else {
            this.$store.commit(FORM.UPDATE_FORM_LOADING, false)
            this.mutationsSubscribe()
          }
        })
      }
    },
    confirmExit: function (event) {
      if (!this.isFormUpdated || this.isCustom) {
        if (window.event !== undefined) window.event.cancelBubble = true
        else event.cancelBubble = true
      } else { return 'message' }
    },
    mutationsSubscribe: function () {
      // Subscribe to store mutation
      this.unSubscribe = this.$store.subscribe((mutation, state) => {
        if (FORM_MUTATIONS_TO_SUBSCRIBE.includes(mutation.type)) {
          this.isFormUpdated = true
          this.unSubscribe()
        }
      })
    }
  },
  mounted: function () {
    // Form : confirm exit or lock panel if form is changed
    this.$nextTick(() => {
      window.onbeforeunload = this.confirmExit
      this.mutationsSubscribe()
    })
  },
  beforeDestroy: function () {
    this.unSubscribe()
  },
  created: function () {
    openMediaLibrary()
  }
})

// DOM Ready general actions
document.addEventListener('DOMContentLoaded', main)
