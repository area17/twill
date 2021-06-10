import { mapGetters, mapState } from 'vuex'
import { BLOCKS } from '@/store/mutations'

export default {
  props: {
    editorName: {
      type: String,
      required: true
    }
  },
  computed: {
    availableBlocks () {
      return this.getAvailableBlocks(this.editorName)
    },
    savedBlocks () {
      return this.blocks(this.editorName)
    },
    allSavedBlocks () {
      return this.used && Object.keys(this.used).reduce((acc, editorName) => acc.concat(this.used[editorName]), [])
    },
    hasBlockActive () {
      return Object.keys(this.activeBlock).length > 0
    },
    ...mapState({
      activeBlock: state => state.blocks.active,
      used: state => state.blocks.blocks
    }),
    ...mapGetters({
      getAvailableBlocks: 'availableBlocks',
      blocks: 'blocks',
      editorNames: 'editorNames'
    })
  },
  methods: {
    reorderBlocks (value) {
      this.$store.commit(BLOCKS.REORDER_BLOCKS, {
        editorName: this.editorName,
        value: value
      })
    },
    moveBlock ({ oldIndex, newIndex }) {
      this.$store.commit(BLOCKS.MOVE_BLOCK, {
        editorName: this.editorName,
        oldIndex,
        newIndex
      })
    }
  },
  render () {
    return this.$scopedSlots.default({
      availableBlocks: this.availableBlocks,
      savedBlocks: this.savedBlocks,
      reorderBlocks: this.reorderBlocks,
      moveBlock: this.moveBlock,
      editorNames: this.editorNames,
      hasBlockActive: this.hasBlockActive,
      allSavedBlocks: this.allSavedBlocks,
      activeBlock: this.activeBlock
    })
  }
}
