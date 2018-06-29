import Vue from 'vue'
import store from '@/store'
import { DATATABLE, MODALEDITION, FORM } from '@/store/mutations'
import ACTIONS from '@/store/actions'

// General shared behaviors
import main from '@/main'
import search from '@/main-search'
import openMediaLibrary from '@/behaviors/openMediaLibrary'

// Plugins
import A17Config from '@/plugins/A17Config'
import A17Notif from '@/plugins/A17Notif'

import { mapState } from 'vuex'

// components
import a17Datatable from '@/components/table/Datatable.vue'
import a17NestedDatatable from '@/components/table/nested/NestedDatatable'
import a17Filter from '@/components/Filter.vue'
import a17TableFilters from '@/components/table/TableFilters.vue'
import a17BulkEdit from '@/components/table/BulkEdit.vue'
import a17LangManager from '@/components/LangManager.vue'
import ModalCreate from '@/components/modals/ModalCreate.vue'

// Store modules
import datatable from '@/store/modules/datatable'
import language from '@/store/modules/language'
import form from '@/store/modules/form'
import modalEdition from '@/store/modules/modal-edition'
import attributes from '@/store/modules/attributes'

// LocalStorage
import { getStorage } from '@/utils/localeStorage.js'

// mixins
import { FormatPermalinkMixin } from '@/mixins'

// configuration
Vue.use(A17Config)
Vue.use(A17Notif)

store.registerModule('datatable', datatable)
store.registerModule('language', language)
store.registerModule('form', form)
store.registerModule('modalEdition', modalEdition)
store.registerModule('attributes', attributes)

/* eslint-disable no-new */
/* eslint no-unused-vars: "off" */
window.vm = new Vue({
  store, // inject store to all children
  el: '#app',
  components: {
    'a17-filter': a17Filter,
    'a17-table-filters': a17TableFilters,
    'a17-datatable': a17Datatable,
    'a17-nested-datatable': a17NestedDatatable,
    'a17-bulk': a17BulkEdit,
    'a17-langmanager': a17LangManager,
    'a17-modal-create': ModalCreate
  },
  mixins: [FormatPermalinkMixin],
  computed: {
    hasBulkIds: function () {
      return this.bulkIds.length > 0
    },
    ...mapState({
      localStorageKey: state => state.datatable.localStorageKey,
      baseUrl: state => state.datatable.baseUrl,
      bulkIds: state => state.datatable.bulk
    })
  },
  methods: {
    create: function () {
      if (this.$refs.editionModal) {
        this.$store.commit(MODALEDITION.UPDATE_MODAL_ACTION, '')
        this.$store.commit(MODALEDITION.UPDATE_MODAL_MODE, 'create')
        this.$store.commit(FORM.EMPTY_FORM_FIELDS)
        this.$refs.editionModal.open()
      }
    },
    reloadDatas: function () {
      // reload datas
      this.$store.dispatch(ACTIONS.GET_DATATABLE)
    },
    clearFiltersAndReloadDatas: function () {
      this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
      this.$store.commit(DATATABLE.CLEAR_DATATABLE_FILTER)

      Object.keys(this.$refs).filter(k => {
        return k.indexOf('filterDropdown[') === 0
      }).map(k => {
        this.$refs[k].updateValue()
      })

      this.reloadDatas()
    },
    filterListing: function (formData) {
      let self = this
      this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
      this.$store.commit(DATATABLE.UPDATE_DATATABLE_FILTER, formData || {search: ''})

      this.$nextTick(function () {
        self.reloadDatas()
      })
    }
  },
  mounted: function () {
    if (window.openCreate) this.create()
  },
  created: function () {
    openMediaLibrary()

    let reload = false
    const pageOffset = getStorage(this.localStorageKey + '_page-offset')
    if (pageOffset) {
      this.$store.commit(DATATABLE.UPDATE_DATATABLE_OFFSET, parseInt(pageOffset))
      reload = true
    }

    const columnsVisible = getStorage(this.localStorageKey + '_columns-visible')
    if (columnsVisible) {
      this.$store.commit(DATATABLE.UPDATE_DATATABLE_VISIBLITY, JSON.parse(columnsVisible))
      reload = true
    }

    if (reload) {
      this.reloadDatas()
    }
  }
})

// DOM Ready general actions
document.addEventListener('DOMContentLoaded', main)
