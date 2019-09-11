import { mapState } from 'vuex'
import NOTIFICATION from '@/store/mutations/notification'
import ACTIONS from '@/store/actions'
import { DATATABLE, FORM, LANGUAGE, MODALEDITION } from '@/store/mutations'
import { TableCellPrefix, TableCellSpecificColumns } from '@/components/table/tableCell'

export default {
  props: {
    index: {
      type: Number,
      default: 0
    },
    row: {
      type: Object,
      default: function () {
        return {}
      }
    },
    columns: {
      type: Array,
      default: function () {
        return []
      }
    }
  },
  computed: {
    editInModal: function () {
      return this.row.hasOwnProperty('editInModal') ? this.row.editInModal : false
    },
    editUrl: function () {
      return this.row.hasOwnProperty('edit') ? this.row.edit : '#'
    },
    updateUrl: function () {
      return this.row['updateUrl'] ? this.row['updateUrl'] : '#'
    },
    ...mapState({
      bulkIds: state => state.datatable.bulk
    })
  },
  methods: {
    currentComponent (colName) {
      return TableCellPrefix + colName.toLowerCase()
    },
    currentComponentProps (col) {
      let props = {
        col: col || {},
        row: this.row,
        editUrl: this.editUrl,
        editInModal: Boolean(this.editInModal)
      }

      if (!col) return props

      switch (col.name) {
        case 'bulk':
          props.value = this.row.id
          props.initialValue = this.bulkIds
          break
        case 'languages':
          props.languages = this.row.hasOwnProperty('languages') ? this.row.languages : []
          props.editUrl = this.editUrl
          break
        case 'publish_start_date':
          props.startDate = ''
          props.endDate = ''
          props.textExpired = 'Expired'
          props.textScheduled = 'Scheduled'
          break
        default:
          break
      }
      return props
    },
    editInPlace: function (data) {
      if (data.lang) {
        const lang = data.lang
        this.$store.commit(LANGUAGE.UPDATE_LANG, lang.value)
      }
      if (this.editInModal) {
        const endpoint = this.editInModal
        this.$store.commit(MODALEDITION.UPDATE_MODAL_MODE, 'update')
        this.$store.commit(MODALEDITION.UPDATE_MODAL_ACTION, this.updateUrl)
        this.$store.commit(FORM.UPDATE_FORM_LOADING, true)

        this.$store.dispatch(ACTIONS.REPLACE_FORM, endpoint).then(() => {
          this.$nextTick(function () {
            if (this.$root.$refs.editionModal) this.$root.$refs.editionModal.open()
          })
        }, (errorResponse) => {
          this.$store.commit(NOTIFICATION.SET_NOTIF, {
            message: 'Your content can not be edited, please retry',
            variant: 'error'
          })
        })
      }
    },
    cellClasses: function (col, prefix) {
      return {
        [prefix + '--icon']: col.name === 'featured' || col.name === 'published',
        [prefix + '--bulk']: col.name === 'bulk',
        [prefix + '--thumb']: col.name === 'thumbnail',
        [prefix + '--draggable']: col.name === 'draggable',
        [prefix + '--languages']: col.name === 'languages',
        [prefix + '--nested']: col.name === 'nested',
        [prefix + '--nested--parent']: col.name === 'nested' && this.nestedDepth === 0,
        [prefix + '--name']: col.name === 'name'
      }
    },
    isSpecificColumn: function (col) {
      return TableCellSpecificColumns.includes(col.name)
    },
    tableCellUpdate: function (data) {
      switch (data.col) {
        case 'published':
          this.togglePublish(data.row)
          break
        case 'bulk':
          this.toggleBulk(data.row)
          break
        case 'featured':
          this.toggleFeatured(data.row)
          break
      }
    },
    toggleFeatured: function (row) {
      if (!row.hasOwnProperty('deleted')) {
        this.$store.dispatch(ACTIONS.TOGGLE_FEATURE, row)
      } else {
        this.$store.commit(NOTIFICATION.SET_NOTIF, {
          message: 'You can’t feature/unfeature a deleted item, please restore it first.',
          variant: 'error'
        })
      }
    },
    toggleBulk: function (row) {
      // We cant use the vmodel of the a17-checkbox directly because the checkboxes are in separated components (so the model is not shared)
      this.$store.commit(DATATABLE.UPDATE_DATATABLE_BULK, row.id)
    },
    togglePublish: function (row) {
      if (!row.hasOwnProperty('deleted')) {
        this.$store.dispatch(ACTIONS.TOGGLE_PUBLISH, row)
      } else {
        this.$store.commit(NOTIFICATION.SET_NOTIF, {
          message: 'You can’t publish/unpublish a deleted item, please restore it first.',
          variant: 'error'
        })
      }
    },
    restoreRow: function (row) {
      this.$store.dispatch(ACTIONS.RESTORE_ROW, row)
    },
    deleteRow: function (row) {
      // open confirm dialog if any
      if (this.$root.$refs.warningDeleteRow) {
        this.$root.$refs.warningDeleteRow.open(() => {
          this.$store.dispatch(ACTIONS.DELETE_ROW, row)
        })
      } else {
        this.$store.dispatch(ACTIONS.DELETE_ROW, row)
      }
    }
  }
}
