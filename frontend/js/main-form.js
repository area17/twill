// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import { mapState } from 'vuex'
import store from '@/store'

// General behaviors
import main from '@/main'
import openMediaLibrary from '@/behaviors/openMediaLibrary'

// Page Components
import a17StickyNav from '@/components/StickyNav.vue'
import a17TitleEditor from '@/components/TitleEditor.vue'
import a17Langswitcher from '@/components/LangSwitcher.vue'
import a17Fieldset from '@/components/Fieldset.vue'
import a17Publisher from '@/components/Publisher.vue'
import a17Content from '@/components/Content.vue'
import a17Repeater from '@/components/Repeater.vue'
import a17LocationField from '@/components/LocationField.vue'
import a17Multiselect from '@/components/MultiSelect.vue'
import a17Singleselect from '@/components/SingleSelect.vue'

// Browser
import a17Browser from '@/components/Browser.vue'

// Overlay Previewer
import a17Overlay from '@/components/Overlay.vue'
import a17Previewer from '@/components/Previewer.vue'

// Plugins
import A17Config from '@/plugins/A17Config'
import A17Notif from '@/plugins/A17Notif'

// Loader
import a17Spinner from '@/components/Spinner.vue'

// configuration
Vue.use(A17Config)
Vue.use(A17Notif)

// Store Modules
import form from '@/store/modules/form'
import publication from '@/store/modules/publication'
import content from '@/store/modules/content'
import language from '@/store/modules/language'
import revision from '@/store/modules/revision'
import browser from '@/store/modules/browser'
import repeaters from '@/store/modules/repeaters'

store.registerModule('form', form)
store.registerModule('publication', publication)
store.registerModule('content', content)
store.registerModule('language', language)
store.registerModule('revision', revision)
store.registerModule('browser', browser)
store.registerModule('repeaters', repeaters)

// Browser
Vue.component('a17-repeater', a17Repeater)
Vue.component('a17-browser', a17Browser)

// Form : radios and checkboxes
Vue.component('a17-multiselect', a17Multiselect)
Vue.component('a17-singleselect', a17Singleselect)

// Form: map component
Vue.component('a17-locationfield', a17LocationField)

// Preview
Vue.component('a17-overlay', a17Overlay)
Vue.component('a17-previewer', a17Previewer)

// Blocks
const importedBlocks = require.context('@/components/blocks/', true, /\.(js|vue)$/i)
importedBlocks.keys().map(block => {
  const blockForName = block.replace(/customs\//, '')
  const blockName = blockForName.match(/\w+/)[0].replace(/([a-z])([A-Z])/g, '$1-$2').replace(/\s+/g, '-').toLowerCase()
  if (blockName !== 'block') {
    return Vue.component('a17-' + blockName, importedBlocks(block))
  }
})

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
Window.vm = new Vue({
  store, // inject store to all children
  el: '#app',
  components: {
    'a17-spinner': a17Spinner,
    'a17-sticky-nav': a17StickyNav,
    'a17-title-editor': a17TitleEditor,
    'a17-langswitcher': a17Langswitcher,
    'a17-fieldset': a17Fieldset,
    'a17-content': a17Content,
    'a17-publisher': a17Publisher
  },
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
      loading: state => state.form.loading
    })
  },
  methods: {
    submitForm: function (event) {
      let self = this

      if (!this.loading) {
        this.isFormUpdated = false
        this.$store.commit('updateFormLoading', true)

        self.$nextTick(function () { // let's wait for the loading state to be properly deployed (used to save wysiwyg fields)
          self.$store.dispatch('saveFormData', document.activeElement.name)
        })
      }
    },
    confirmExit: function (event) {
      if (!this.isFormUpdated) {
        if (window.event !== undefined) window.event.cancelBubble = true
        else event.cancelBubble = true
      } else { return 'message' }
    }
  },
  mounted: function () {
    // Form : confirm exit or lock panel if form is changed
    this.$nextTick(function () {
      window.onbeforeunload = this.confirmExit
      // Subscribe to store mutation
      this.unSubscribe = this.$store.subscribe((mutation, state) => {
        console.log('subscribe')
        this.isFormUpdated = true
      })
    })
  },
  watch: {
    'isFormUpdated': function (newVal) {
      if (newVal) this.unSubscribe()
    }
  },
  beforeDestroy: function () {
    this.unSubscribe()
  },
  created: function () {
    openMediaLibrary()
  }
})

// User header dropdown
/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
Window.vheader = new Vue({ el: '#headerUser' })

// DOM Ready general actions
document.addEventListener('DOMContentLoaded', main)
