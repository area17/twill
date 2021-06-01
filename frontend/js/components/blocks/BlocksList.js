import { mapGetters, mapState } from 'vuex'
import CONTENT from '@/store/mutations/content'

export default {
  props: {
    section: {
      type: String
    }
  },
  computed: {
    availableBlocks () {
      return this.section ? this.availableBlocksBySection(this.section) : this.allAvailableBlocks
    },
    savedBlocks () {
      return this.section ? this.savedBlocksBySection(this.section) : this.allSavedBlocks
    },
    multipleSections () {
      return this.sections.length > 1
    },
    hasBlockActive () {
      return Object.keys(this.activeBlock).length > 0
    },
    ...mapState({
      activeBlock: state => state.content.active
    }),
    ...mapGetters(
      [
        'allSavedBlocks',
        'allAvailableBlocks',
        'availableBlocksBySection',
        'savedBlocksBySection',
        'sections'
      ]
    )
  },
  methods: {
    reorderBlocks (value) {
      this.$store.commit(CONTENT.REORDER_BLOCKS, {
        section: this.section,
        value: value
      })
    },
    moveBlock ({ oldIndex, newIndex }) {
      this.$store.commit(CONTENT.MOVE_BLOCK, {
        section: this.section,
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
      sections: this.sections,
      multipleSections: this.multipleSections,
      hasBlockActive: this.hasBlockActive,
      activeBlock: this.activeBlock
    })
  }
}
