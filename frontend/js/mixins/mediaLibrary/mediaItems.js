import { mapState } from 'vuex'

export default {
  props: {
    items: {
      type: Array,
      default: () => []
    },
    selectedItems: {
      type: Array,
      default: () => []
    },
    usedItems: {
      type: Array,
      default: () => []
    }
  },
  computed: {
    ...mapState({
      itemsLoading: state => state.mediaLibrary.loading
    })
  },
  methods: {
    isSelected: function (id) {
      const result = this.selectedItems.filter(function (item) {
        return item.id === id
      })

      return result.length > 0
    },
    isUsed: function (id) {
      return !!this.usedItems.find(item => item.id === id)
    },
    toggleSelection: function (id) {
      this.$emit('change', id)
    },
    shiftToggleSelection: function (id) {
      this.$emit('shiftChange', id, true)
    }
  }
}
