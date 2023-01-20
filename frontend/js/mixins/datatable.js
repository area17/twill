import { mapGetters,mapState } from 'vuex'

import ACTIONS from '@/store/actions'
import { DATATABLE } from '@/store/mutations/index'

export default {
  props: {
    nested: {
      type: Boolean,
      default: false
    },
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
    rows: {
      get () {
        return this.$store.state.datatable.data
      },
      set (value) {
        const isChangingParents = (this.rows.length !== value.length)
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_DATA, value)
        this.saveNewTree(isChangingParents)
      }
    },
    isEmpty: function () {
      return this.rows.length <= 0
    },
    isEmptyDatable: function () {
      return { 'datatable__table--empty': this.isEmpty }
    },
    ...mapState({
      columns: state => state.datatable.columns
    }),
    ...mapGetters([
      'visibleColumns',
      'hideableColumns',
      'visibleColumnsNames'
    ])
  },
  methods: {
    saveNewTree: function (isChangingParents) {
      const isNestedAction = isChangingParents ? true : this.nested
      const action = isNestedAction ? ACTIONS.SET_DATATABLE_NESTED : ACTIONS.SET_DATATABLE

      const save = () => {
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_TRACKER, 0)
        this.$store.dispatch(action)
      }

      // Proof of concepts
      if (isChangingParents) {
        // 2 moves need to happen so we can save the new tree (1 move to remove from list and a second to add to a new list)
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_TRACKER, 1)
        if (this.updateTracker >= 2) save()
      } else {
        // reorder rows
        save()
      }
    }
  }
}
