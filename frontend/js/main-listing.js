import Vue from 'vue'
import store from '@/store'

// General behaviors
import main from '@/main'

// Plugins
import A17Config from '@/plugins/A17Config'

// configuration
Vue.use(A17Config)

import { mapState } from 'vuex'
import a17VueFilters from '@/utils/filters.js'

// components
import a17Datatable from '@/components/table/Datatable.vue'
import a17Filter from '@/components/Filter.vue'
import a17BulkEdit from '@/components/table/BulkEdit.vue'
import a17ModalTitleEditor from '@/components/Modals/ModalTitleEditor.vue'
import ModalValidationButtons from '@/components/Modals/ModalValidationButtons.vue'

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
Window.vm = new Vue({
  store, // inject store to all children
  el: '#app',
  props: {
    pageType: {
      type: String,
      default: ''
    }
  },
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
      navFilters: this.$store.state.datatable.filtersNav,
      navActive: 0
    }
  },
  computed: {
    ...mapState({
      baseUrl: state => state.form.baseUrl,
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
    filterListing: function (formData) {
      this.$store.commit('updateDatablePage', 1)
      this.$store.commit('updateDatableFilter', formData)
      this.reloadDatas()
    },
    filterStatus: function (index, slug) {
      if (this.navActive === index) return
      this.navActive = index
      this.$store.commit('updateDatablePage', 1)
      this.$store.commit('updateDatableFilterStatus', slug)
      this.reloadDatas()
    },
    createItem: function (data) {
      // TODO:  Do something with data to create new element
      const state = data.state
      if (state !== 'add-another') {
        this.$refs.addNewModal.close()
      }
    }
  }
})

// DOM Ready general actions
document.addEventListener('DOMContentLoaded', main)
