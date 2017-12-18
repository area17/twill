import Vue from 'vue'
import store from '@/store'

// General behaviors
import main from '@/main'
import openMediaLibrary from '@/behaviors/openMediaLibrary'

// Plugins
import A17Config from '@/plugins/A17Config'
import A17Notif from '@/plugins/A17Notif'

// configuration
Vue.use(A17Config)
Vue.use(A17Notif)

import { mapState } from 'vuex'
import a17VueFilters from '@/utils/filters.js'

// components
import a17Datatable from '@/components/table/Datatable.vue'
import a17Filter from '@/components/Filter.vue'
import a17BulkEdit from '@/components/table/BulkEdit.vue'
import a17ModalTitleEditor from '@/components/Modals/ModalTitleEditor.vue'
import ModalValidationButtons from '@/components/Modals/ModalValidationButtons.vue'

// Store modules
import datatable from '@/store/modules/datatable'

store.registerModule('datatable', datatable)

// LocalStorage
import { getStorage } from '@/utils/localeStorage.js'

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
Window.vm = new Vue({
  store, // inject store to all children
  el: '#app',
  components: {
    'a17-filter': a17Filter,
    'a17-datatable': a17Datatable,
    'a17-bulk': a17BulkEdit,
    'a17-modal-title-editor': a17ModalTitleEditor,
    'a17-modal-validation': ModalValidationButtons
  },
  filters: a17VueFilters,
  data: function () {
    return {
      inputPermalink: '',
      navFilters: this.$store.state.datatable.filtersNav
    }
  },
  computed: {
    hasBulkIds: function () {
      return this.bulkIds.length > 0
    },
    selectedNav: function () {
      let self = this
      const navItem = self.navFilters.filter(function (n) {
        return n.slug === self.navActive
      })
      return navItem[0]
    },
    ...mapState({
      localStorageKey: state => state.datatable.localStorageKey,
      navActive: state => state.datatable.filter.status,
      baseUrl: state => state.datatable.baseUrl,
      bulkIds: state => state.datatable.bulk
    })
  },
  methods: {
    formatPermalink: function (newValue) {
      const slug = this.$options.filters.slugify(newValue)
      this.inputPermalink = slug
    },
    reloadDatas: function () {
      // reload datas
      this.$store.dispatch('getDatatableDatas')
    },
    clearFiltersAndReloadDatas: function () {
      this.$store.commit('updateDatablePage', 1)
      this.$store.commit('clearDatableFilter')
      this.reloadDatas()
    },
    filterListing: function (formData) {
      this.$store.commit('updateDatablePage', 1)
      this.$store.commit('updateDatableFilter', formData || {search: ''})
      this.reloadDatas()
    },
    filterStatus: function (slug) {
      if (this.navActive === slug) return
      this.$store.commit('updateDatablePage', 1)
      this.$store.commit('updateDatableFilterStatus', slug)
      this.reloadDatas()
    }
  },
  created: function () {
    openMediaLibrary()
    let reload = false
    const pageOffset = getStorage(this.localStorageKey + '_page-offset')
    if (pageOffset) {
      this.$store.commit('updateDatableOffset', parseInt(pageOffset))
      reload = true
    }

    const columnsVisible = getStorage(this.localStorageKey + '_columns-visible')
    if (columnsVisible) {
      this.$store.commit('updateDatableVisibility', JSON.parse(columnsVisible))
      reload = true
    }

    if (reload) {
      this.reloadDatas()
    }
  }
})

// User header dropdown
/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
Window.vheader = new Vue({ el: '#headerUser' })

// DOM Ready general actions
document.addEventListener('DOMContentLoaded', main)
