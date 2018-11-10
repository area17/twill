import { mapGetters, mapState } from 'vuex'
import CONTENT from '@/store/mutations/content'

export default {
  props: {
    section: {
      type: [String],
      default: 'default'
    }
  },
  computed: {
    hasBlockActive () {
      return Object.keys(this.activeBlock).length
    },
    availableBlocks () {
      return this.availableBlocksBySection(this.section)
    },
    savedBlocks () {
      return this.savedBlocksBySection(this.section)
    },
    hasActiveBlock () {
      return Object.keys(this.activeBlock).length > 0
    },
    ...mapState({
      activeBlock: state => state.content.active
    }),
    ...mapGetters(
      [
        'activeBlockIndex',
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
    }
  },
  render () {
    return this.$scopedSlots.default({
      hasActiveBlock: this.hasActiveBlock,
      activeBlock: this.activeBlock,
      activeBlockIndex: this.activeBlockIndex(this.section),
      availableBlocks: this.availableBlocks,
      savedBlocks: this.savedBlocks,
      reorderBlocks: this.reorderBlocks,
      sections: this.sections
    })
  }
}
