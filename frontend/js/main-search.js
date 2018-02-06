// Search Vue app
import Vue from 'vue'

// Plugins
import A17Config from '@/plugins/A17Config'

// configuration
Vue.use(A17Config)

// components
import a17Search from '@/components/Search.vue'

const A17SearchApp = new Vue({
  el: '#searchApp',
  components: {
    'a17-search': a17Search
  },
  data: function () {
    return {
    }
  },
  computed: {
  },
  methods: {
  },
  mounted: function () {
    console.log(this.$el)
  },
  created: function () {
    console.log('search created')
  }
})

export default A17SearchApp
