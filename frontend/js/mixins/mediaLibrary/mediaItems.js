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
    isSelected: function (item, keys = ['id']) {
      return Boolean(this.selectedItems.find(sItem => keys.every(key => sItem[key] === item[key])))
    },
    isUsed: function (item, keys = ['id']) {
      return Boolean(this.usedItems.find(uItem => keys.every(key => uItem[key] === item[key])))
    },
    toggleSelection: function (item) {
      this.$emit('change', item)
    },
    shiftToggleSelection: function (item) {
      this.$emit('shiftChange', item, true)
    }
  }
}
