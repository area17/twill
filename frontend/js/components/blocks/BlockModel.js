import CONTENT from '@/store/mutations/content'

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
    move (oldIndex, newIndex) {
      if (oldIndex === newIndex) return
      this.$store.commit(CONTENT.MOVE_BLOCK, {
        section: this.section,
        oldIndex: oldIndex,
        newIndex: newIndex
      })
    },
    duplicate (index) {
      this.$store.commit(CONTENT.DUPLICATE_BLOCK, {
        section: this.section,
        index: index
      })
    },
    remove (index) {
      console.log('delete-block')
      // open confirm dialog if any
      this.$store.commit(CONTENT.DELETE_BLOCK, {
        section: this.section,
        index: index
      })
    }
  },
  render () {
    return this.$scopedSlots.default({
      block: this.block,
      add: this.add,
      remove: this.remove,
      move: this.move,
      duplicate: this.duplicate
    })
  }
}
