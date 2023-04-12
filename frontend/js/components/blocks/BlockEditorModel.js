import { mapGetters, mapState } from 'vuex'

import ACTIONS from '@/store/actions'
import { BLOCKS } from '@/store/mutations'

export default {
  props: {
    block: {
      type: Object
    },
    editorName: {
      type: String,
      required: true
    }
  },
  computed: {
    blockIndex () {
      return this.block ? this.getBlockIndex(this.block, this.editorName) : 0
    },
    isActive () {
      return this.block && Object.keys(this.activeBlock).length > 0 ? this.block.id === this.activeBlock.id : false
    },
    ...mapState({
      activeBlock: state => state.blocks.active
    }),
    ...mapGetters({
      getBlockIndex: 'blockIndex'
    })
  },
  methods: {
    add (block, index = -1) {
      this.$store.commit(BLOCKS.ADD_BLOCK, {
        editorName: this.editorName,
        block: {
          id: this.setBlockID(),
          title: block.title,
          type: block.component,
          icon: block.icon,
          attributes: block.attributes
        },
        index
      })
    },
    edit (index = this.blockIndex) {
      this.$store.commit(BLOCKS.ACTIVATE_BLOCK, {
        editorName: this.editorName,
        index
      })
    },
    unEdit () {
      this.$store.commit(BLOCKS.ACTIVATE_BLOCK, {
        editorName: this.editorName,
        index: -1
      })
    },
    move (newIndex) {
      if (this.blockIndex === newIndex) return
      this.$store.commit(BLOCKS.MOVE_BLOCK, {
        editorName: this.editorName,
        oldIndex: this.blockIndex,
        newIndex
      })
    },
    duplicate () {
      const block = Object.assign({}, this.block)
      this.$store.commit(BLOCKS.DUPLICATE_BLOCK, {
        editorName: this.editorName,
        index: this.blockIndex,
        block,
        id: this.setBlockID()
      })
    },
    remove () {
      this.unEdit()
      this.$store.commit(BLOCKS.DELETE_BLOCK, {
        editorName: this.editorName,
        index: this.blockIndex
      })
    },
    cloneBlock () {
      this.$store.dispatch(ACTIONS.DUPLICATE_BLOCK, {
        editorName: this.editorName,
        futureIndex: this.blockIndex + 1,
        block: this.block,
        id: Date.now() + Math.floor(Math.random() * 1000)
      })
    },
    setBlockID () {
      return Date.now() + Math.floor(Math.random() * 1000)
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
      duplicate: this.duplicate,
      cloneBlock: this.cloneBlock
    })
  }
}
