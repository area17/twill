import CONTENT from '@/store/mutations/content'
import { mapGetters } from 'vuex'

export default {
  props: {
    block: {
      type: Object,
      default: () => {}
    },
    section: {
      type: String,
      default: 'default'
    }
  },
  computed: {
    blockIndex () {
      console.log('blockIndex', this.block, this.section)
      return this.blockIndexBySection(this.block, this.section)
    },
    ...mapGetters([
      'blockIndexBySection'
    ])
  },
  methods: {
    add (block, index = -1) {
      console.log('BlockList - add block', block)
      this.$store.commit(CONTENT.ADD_BLOCK, {
        section: this.section,
        block: {
          title: block.title,
          type: block.component,
          icon: block.icon,
          attributes: block.attributes
        },
        index: index
      })
    },
    move (newIndex) {
      if (this.blockIndex === newIndex) return
      this.$store.commit(CONTENT.MOVE_BLOCK, {
        section: this.section,
        oldIndex: this.blockIndex,
        newIndex: newIndex
      })
    },
    duplicate () {
      this.$store.commit(CONTENT.DUPLICATE_BLOCK, {
        section: this.section,
        index: this.blockIndex
      })
    },
    remove () {
      console.log('delete-block')
      // open confirm dialog if any
      this.$store.commit(CONTENT.DELETE_BLOCK, {
        section: this.section,
        index: this.blockIndex
      })
    }
  },
  render () {
    return this.$scopedSlots.default({
      block: this.block,
      blockIndex: this.blockIndex,
      add: this.add,
      remove: this.remove,
      move: this.move,
      duplicate: this.duplicate
    })
  }
}
