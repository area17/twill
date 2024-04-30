// Search Vue app
import { createApp } from 'vue'

// Plugins
import A17Config from '@/plugins/A17Config'

// components
import a17Search from '@/components/Search.vue'

const idSearch = 'searchApp'
const vueSearchApp = {
  components: {
    'a17-search': a17Search
  },
  props: {
    topSpacing: {
      type: Number,
      default: 60
    }
  },
  data: function () {
    return {
      open: false,
      opened: false,
      top: this.topSpacing
    }
  },
  computed: {
    positionStyle: function () {
      return {
        top: this.top + 'px'
      }
    }
  },
  methods: {
    afterAnimate: function () {
      this.opened = true
    },
    toggleSearch: function () {
      this.open = !this.open
      this.top = this.topSpacing - (window.pageYOffset || document.documentElement.scrollTop)

      if (this.open) {
        document.addEventListener('keydown', this.handleKeyDown, false)
      } else {
        this.opened = false
        document.removeEventListener('keydown', this.handleKeyDown, false)
      }
    },
    handleKeyDown: function (event) {
      if (event.keyCode && event.keyCode === 27) { // esc key
        this.toggleSearch()
      }
    }
  }
}

const app = createApp(vueSearchApp)

// configuration
app.use(A17Config)

const A17SearchApp = document.getElementById(idSearch) ? app.mount('#searchApp') : false
export default A17SearchApp
