import { mapGetters, mapState } from 'vuex'

import ACTIONS from '@/store/actions'
import { BLOCKS } from '@/store/mutations'

export default {
  props: {
    editorName: {
      type: String,
      required: true
    },
    availabilityId: {
      type: String,
    }
  },
  computed: {
    availableBlocks () {
      return this.getAvailableBlocks(this.availabilityId ? this.availabilityId : this.editorName.split('|').pop())
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
    reorderBlocks (value) {
      this.$store.commit(BLOCKS.REORDER_BLOCKS, {
        editorName: this.editorName,
        value
      })
    },
    addBlock (block, editorName, index = -1) {
      this.$store.commit(BLOCKS.ADD_BLOCK, {
        editorName,
        block: {
          ...block,
          type: block.type || block.component
        },
        index
      })
    },
    moveBlock ({ oldIndex, newIndex }) {
      this.$store.commit(BLOCKS.MOVE_BLOCK, {
        editorName: this.editorName,
        oldIndex,
        newIndex
      })
    },
    moveBlockToEditor (block, editorName, index, futureIndex) {
      this.$store.dispatch(ACTIONS.MOVE_BLOCK_TO_EDITOR, {
        block,
        editorName,
        index,
        futureIndex,
        id: Date.now() + Math.floor(Math.random() * 1000)
      })
    },
    cloneBlock ({ block, index }) {
      this.$store.dispatch(ACTIONS.DUPLICATE_BLOCK, {
        editorName: this.editorName,
        futureIndex: index,
        block,
        id: Date.now() + Math.floor(Math.random() * 1000)
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
      activeBlock: this.activeBlock,
      addBlock: this.addBlock,
      moveBlockToEditor: this.moveBlockToEditor,
      cloneBlock: this.cloneBlock
    })
  }
}
