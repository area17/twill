import CONTENT from '@/store/mutations/content'
import { mapGetters, mapState } from 'vuex'

export default {
  props: {
    block: {
      type: Object
    },
    section: {
      type: String,
      default: 'default'
    }
  },
  computed: {
    blockIndex () {
      return this.block ? this.blockIndexBySection(this.block, this.section) : 0
    },
    isActive () {
      return this.block && Object.keys(this.activeBlock).length > 0 ? this.block.id === this.activeBlock.id : false
    },
    ...mapState({
      activeBlock: state => state.content.active
    }),
    ...mapGetters([
      'blockIndexBySection'
    ])
  },
  methods: {
    add (block, index = -1) {
      this.$store.commit(CONTENT.ADD_BLOCK, {
        section: this.section,
        block: {
          id: this.setBlockID(),
          title: block.title,
          type: block.component,
          icon: block.icon,
          attributes: block.attributes
        },
        index: index
      })
    },
    edit (index = this.blockIndex) {
      this.$store.commit(CONTENT.ACTIVATE_BLOCK, {
        section: this.section,
        index: index
      })
    },
    unEdit () {
      this.$store.commit(CONTENT.ACTIVATE_BLOCK, {
        section: this.section,
        index: -1
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
      let block = Object.assign({}, this.block)
      block.id = this.setBlockID()
      this.$store.commit(CONTENT.DUPLICATE_BLOCK, {
        section: this.section,
        index: this.blockIndex,
        block: block
      })
    },
    remove () {
      this.unEdit()
      this.$store.commit(CONTENT.DELETE_BLOCK, {
        section: this.section,
        index: this.blockIndex
      })
    },
    setBlockID () {
      return Date.now()
    }
  },
  render () {
    return this.$scopedSlots.default({
      block: this.block,
      blockIndex: this.blockIndex,
      add: this.add,
      edit: this.edit,
      unEdit: this.unEdit,
      isActive: this.isActive,
      remove: this.remove,
      move: this.move,
      duplicate: this.duplicate
    })
  }
}
