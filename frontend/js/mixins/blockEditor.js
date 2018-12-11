import cloneDeep from 'lodash/cloneDeep'

export default {
  props: {
    section: {
      type: String,
      default: 'default'
    },
    blocks: {
      type: Array,
      default: () => []
    },
    savedBlocksLength: {
      type: Number,
      default: 0
    }
  },
  methods: {
    addAndEditBlock (add, edit, { block, index }) {
      window.PREVSTATE = cloneDeep(this.$store.state)
      console.log('add-and-edit-block', { block, index })
      add(block, index)
      edit(index)
    },
    saveBlock (fn, index) {
      if (window.hasOwnProperty('PREVSTATE')) delete window.PREVSTATE
      this.unselectBlock(fn, index)
      this.$emit('block:save', index)
    },
    cancelBlock (fn, index) {
      if (window.hasOwnProperty('PREVSTATE')) {
        console.warn('Store - Restore previous Store state')
        this.$store.replaceState(window.PREVSTATE)
      }
      this.unselectBlock(fn, index)
      this.$emit('block:cancel', index)
    },
    selectBlock (fn, index) {
      window.PREVSTATE = cloneDeep(this.$store.state)
      fn()
      this.$emit('block:select', index)
    },
    unselectBlock (fn, index) {
      fn()
      if (window.hasOwnProperty('PREVSTATE')) delete window.PREVSTATE
      this.$emit('block:unselect', index)
    },
    moveBlock (index) {
      this.$emit('block:move', index)
    },
    deleteBlock (fn) {
      if (this.$root.$refs.warningContentEditor) {
        this.$root.$refs.warningContentEditor.open(() => {
          fn()
          this.$emit('block:delete')
        })
      } else {
        fn()
        this.$emit('block:delete')
      }
    }
  }
}
