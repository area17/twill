export default {
  props: {
    block: {
      type: Object,
      default: () => {}
    },
    blockIndex: {
      type: Number,
      default: 0
    },
    blocksLength: {
      type: Number,
      default: 0
    }
  },
  methods: {
    selectBlock () {
      this.$emit('block:select')
    },
    unselectBlock () {
      this.$emit('block:unselect')
    },
    deleteBlock () {
      this.$emit('block:delete')
    },
    moveBlock (index) {
      this.$emit('block:move', index)
    },
    toggleBlockDropdown () {
      if (this.blocksLength > 1 && this.$refs.blockDropdown) {
        this.$refs.blockDropdown.toggle()
      }
    }
  }
}
