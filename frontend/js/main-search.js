// Search Vue app
import Vue from 'vue'

// Plugins
import A17Config from '@/plugins/A17Config'

// configuration
Vue.use(A17Config)

// components
import a17Search from '@/components/Search.vue'

const idSearch = 'searchApp'
const vueSearchApp = {
  el: '#searchApp',
  components: {
    'a17-search': a17Search
  },
  data: function () {
    return {
      open: false,
      opened: false
    }
  },
  methods: {
    afterAnimate: function () {
      this.opened = true
    },
    toggleSearch: function () {
      this.open = !this.open
      if (this.open) {
        document.addEventListener('keydown', this.handleKeyDown, false)
      } else {
        this.opened = false
        document.removeEventListener('keydown', this.handleKeyDown, false)
      }
    },
    handleKeyDown: function (event) {
      if (event.keyCode && event.keyCode === 27) {
        this.toggleSearch()
      }
    }
  },
  mounted: function () {
  },
  created: function () {
  }
}

const A17SearchApp = document.getElementById(idSearch) ? new Vue(vueSearchApp) : false
export default A17SearchApp
