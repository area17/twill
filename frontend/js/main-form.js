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
import a17Content from '@/components/Content.vue'
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
import content from '@/store/modules/content'
import language from '@/store/modules/language'
import revision from '@/store/modules/revision'
import browser from '@/store/modules/browser'
import repeaters from '@/store/modules/repeaters'
import parents from '@/store/modules/parents'
import attributes from '@/store/modules/attributes'

// mixins
import formatPermalink from '@/mixins/formatPermalink'
import editorMixin from '@/mixins/editor.js'

// configuration
Vue.use(A17Config)
Vue.use(A17Notif)

store.registerModule('form', form)
store.registerModule('publication', publication)
store.registerModule('content', content)
store.registerModule('language', language)
store.registerModule('revision', revision)
store.registerModule('browser', browser)
store.registerModule('repeaters', repeaters)
store.registerModule('parents', parents)
store.registerModule('attributes', attributes)

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
const importedBlocks = require.context('@/components/blocks/', true, /\.(js|vue)$/i)
importedBlocks.keys().map(block => {
  const blockForName = block.replace(/customs\//, '')
  const blockName = blockForName.match(/\w+/)[0].replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, '-').toLowerCase()
  if (blockName !== 'block') {
    return Vue.component('a17-' + blockName, importedBlocks(block).default)
  }
})

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
window.vm = new Vue({
  store, // inject store to all children
  el: '#app',
  components: {
    'a17-spinner': a17Spinner,
    'a17-sticky-nav': a17StickyNav,
    'a17-title-editor': a17TitleEditor,
    'a17-langswitcher': a17Langswitcher,
    'a17-fieldset': a17Fieldset,
    'a17-content': a17Content,
    'a17-publisher': a17Publisher,
    'a17-page-nav': a17PageNav
  },
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
      editor: state => state.content.editor,
      isCustom: state => state.form.isCustom
    }),
    ...mapGetters([
      'getSaveType'
    ])
  },
  methods: {
    submitForm: function (event) {
      if (!this.loading) {
        this.isFormUpdated = false
        this.$store.commit(FORM.UPDATE_FORM_LOADING, true)
        this.unSubscribe()
        this.$nextTick(() => { // let's wait for the loading state to be properly deployed (used to save wysiwyg fields)
          const saveType = this.getSaveType || document.activeElement.name
          this.$store.dispatch(ACTIONS.SAVE_FORM, saveType).then(() => {
            this.mutationsSubscribe()
          })
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
