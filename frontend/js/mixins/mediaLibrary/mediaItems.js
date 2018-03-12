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
      return Boolean(this.selectedItems.find(item => item.id === id))
    },
    isUsed: function (id) {
      return Boolean(this.usedItems.find(item => item.id === id))
    },
    toggleSelection: function (id) {
      this.$emit('change', id)
    },
    shiftToggleSelection: function (id) {
      this.$emit('shiftChange', id, true)
    }
  }
}
