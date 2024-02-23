import cloneDeep from 'lodash/cloneDeep'

export default {
  props: {
    editorName: {
      type: String,
      required: true
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
  provide() {
    return {
      inContentEditor: true,
    }
  },
  methods: {
    addAndEditBlock (add, edit, { block, index }) {
      window[process.env.VUE_APP_NAME].PREVSTATE = cloneDeep(this.$store.state)
      add(block, index)
      edit(index)
    },
    saveBlock (fn, index) {
      if (window[process.env.VUE_APP_NAME].hasOwnProperty('PREVSTATE')) delete window[process.env.VUE_APP_NAME].PREVSTATE
      this.unselectBlock(fn, index)
      this.$emit('block:save', index)
    },
    cancelBlock (fn, index) {
      if (window[process.env.VUE_APP_NAME].hasOwnProperty('PREVSTATE')) {
        // eslint-disable-next-line
        console.warn('Store - Restore previous Store state')
        this.$store.replaceState(window[process.env.VUE_APP_NAME].PREVSTATE)
      }
      this.unselectBlock(fn, index)
      this.$emit('block:cancel', index)
    },
    selectBlock (fn, index) {
      window[process.env.VUE_APP_NAME].PREVSTATE = cloneDeep(this.$store.state)
      fn()
      this.$emit('block:select', index)
    },
    unselectBlock (fn, index) {
      fn()
      if (window[process.env.VUE_APP_NAME].hasOwnProperty('PREVSTATE')) delete window[process.env.VUE_APP_NAME].PREVSTATE
      this.$emit('block:unselect', index)
    },
    moveBlock (index) {
      this.$emit('block:move', index)
    },
    cloneBlock (fn) {
      fn()
      this.$emit('block:clone')
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
