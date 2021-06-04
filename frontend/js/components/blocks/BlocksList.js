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
      used: state => state.blocks.blocks,
      editorNames: state => state.blocks.editorNames
    }),
    ...mapGetters({
      getAvailableBlocks: 'availableBlocks',
      blocks: 'blocks'
    })
  },
  methods: {
    addBlock (block, index = -1) {
      this.$store.commit(BLOCKS.ADD_BLOCK, {
        section: block.name,
        block: {
          id: Date.now(),
          title: block.title,
          type: block.component,
          icon: block.icon,
          attributes: block.attributes
        },
        index
      })
    },
    removeBlock (block, index = -1) {
      this.$store.commit(BLOCKS.DELETE_BLOCK, {
        section: block.name,
        index
      })
    },
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
      activeBlock: this.activeBlock,
      addBlock: this.addBlock,
      removeBlock: this.removeBlock
    })
  }
}
