import { DATATABLE } from '@/store/mutations/index'

export default {
  props: {
    /**
     * Enable bulk edition on dataTable. Actions are publish / unPublish, delete...
     * @type {Boolean}
     */
    bulkeditable: {
      type: Boolean,
      default: true
    },
    /**
     * The message to show when the listing is empty
     * @type {string}
     */
    emptyMessage: {
      type: String,
      default: ''
    }
  },
  computed: {
    isEmpty: function () {
      return this.rows.length <= 0
    },
    isEmptyDatable: function () {
      return {'datatable__table--empty': this.isEmpty}
    },
    rows: {
      get () {
        return this.$store.state.datatable.data
      },
      set (value) {
        const isChangingParents = (this.rows.length !== value.length)
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_DATA, value)
        this.saveNewTree(isChangingParents)
      }
    }
  }
}
