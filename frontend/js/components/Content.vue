<template>
  <div class="content">
    <draggable class="content__container" v-model="blocks" :options="dragOptions">
      <transition-group name="draggable_list" tag='div'>
        <div class="content__item" v-for="(block, index) in blocks" :key="block.id">
          <a17-block :block="block" :index="index" :opened="opened" :closed="closed" @expand="setOpened" ref="blockList">
            <button type="button" slot="dropdown-add" v-if="availableBlocks.length" v-for="availableBlock in availableBlocks" :key="availableBlock.component" @click="addBlock(availableBlock, index + 1)"><span v-svg :symbol="availableBlock.icon"></span> {{ availableBlock.title }}</button>
            <div slot="dropdown-action">
              <button type="button" @click="collapseAllBlocks()" v-if="opened">Collapse all</button>
              <button type="button" @click="expandAllBlocks()" v-else>Expand all</button>
              <button v-if="editor" type="button" @click="openEditor(index)">Open in editor</button>
              <button type="button" @click="duplicateBlock(index)">Create another</button>
              <button type="button" @click="deleteBlock(index)">Delete</button>
            </div>
            <button type="button" slot="dropdown-numbers" v-for="n in blocks.length" @click="moveBlock(index, n - 1)" :key="n">{{ n }}</button>
          </a17-block>
        </div>
      </transition-group>
    </draggable>

    <div class="content__actions">
      <a17-dropdown ref="blocksDropdown" position="top-center" :arrow="true" :offset="10" v-if="availableBlocks.length" :maxHeight="430">
        <a17-button size="small" variant="action" @click="$refs.blocksDropdown.toggle()">{{ title }}</a17-button>
        <div slot="dropdown__content">
          <button type="button" v-for="availableBlock in availableBlocks" :key="availableBlock.component" @click="addBlock(availableBlock, -1)"><span class="content__icon" v-svg :symbol="availableBlock.icon"></span>{{ availableBlock.title }}</button>
        </div>
      </a17-dropdown>
      <div class="content__secondaryActions">
        <a href="#" v-if="editor" class="f--link f--link-underlined--o" @click.prevent="openEditor(-1)">Open in editor</a>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { CONTENT } from '@/store/mutations'

  import draggable from 'vuedraggable'
  import draggableMixin from '@/mixins/draggable'
  import editorMixin from '@/mixins/editor.js'
  import Block from '@/components/blocks/Block.vue'

  export default {
    name: 'A17Content',
    components: {
      'a17-block': Block,
      draggable
    },
    mixins: [draggableMixin, editorMixin],
    props: {
      title: {
        type: String,
        default: ''
      }
    },
    data: function () {
      return {
        opened: true,
        closed: false,
        handle: '.block__handle' // drag handle
      }
    },
    computed: {
      blocks: {
        get () {
          return this.savedBlocks
        },
        set (value) {
          this.$store.commit(CONTENT.REORDER_BLOCKS, value)
        }
      },
      ...mapState({
        editor: state => state.content.editor,
        savedBlocks: state => state.content.blocks,
        availableBlocks: state => state.content.available
      })
    },
    methods: {
      setOpened: function (value) {
        const allHidden = this.$refs.blockList.every((block) => !block.visible)
        if (allHidden) {
          this.opened = false
          this.closed = true
        }

        if (value) this.opened = true
      },
      addDropdownId: function (index) {
        return `addBlock${index}Dropdown`
      },
      toggleDropdown: function (index) {
        const dropdownId = this.addDropdownId(index)
        const dropdown = this.$refs[dropdownId][0]
        if (dropdown) dropdown.toggle()
      },
      moveBlock: function (oldIndex, newIndex) {
        if (oldIndex !== newIndex) {
          this.$store.commit(CONTENT.MOVE_BLOCK, {
            oldIndex: oldIndex,
            newIndex: newIndex
          })
        }
      },
      addBlock: function (block, fromIndex) {
        this.opened = true
        let newBlock = {
          title: block.title,
          type: block.component,
          icon: block.icon,
          attributes: block.attributes
        }

        this.$store.commit(CONTENT.ADD_BLOCK, {
          block: newBlock,
          index: fromIndex
        })
      },
      duplicateBlock: function (index) {
        this.opened = true
        this.$store.commit(CONTENT.DUPLICATE_BLOCK, index)
      },
      deleteBlock: function (index) {
        // open confirm dialog if any
        if (this.$root.$refs.warningContentEditor) {
          this.$root.$refs.warningContentEditor.open(() => {
            this.$store.commit(CONTENT.DELETE_BLOCK, index)
          })
        } else {
          this.$store.commit(CONTENT.DELETE_BLOCK, index)
        }
      },
      collapseAllBlocks: function () {
        this.opened = false
        this.closed = true
      },
      expandAllBlocks: function () {
        this.opened = true
        this.closed = false
      }
    },
    mounted: function () {
      let self = this
      // if there are blocks, these should be all collapse by default
      this.$nextTick(function () {
        if (self.savedBlocks.length > 3) self.collapseAllBlocks()
      })
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .content {
    margin-top:20px; // margin-top:35px;
  }

  .content__container {
    margin-bottom:20px;

    + .dropdown {
      display:inline-block;
    }
  }

  .content__actions {
    display:flex;
  }

  .content__secondaryActions {
    flex-grow:1;
    text-align:right;
    margin-left:20px;
    padding-top:8px;
  }

  .content__item {
    border:1px solid $color__border;
    border-top:0 none;

    &.sortable-ghost {
      opacity:0.5;
    }
  }

  .content__actions button .content__icon {
    margin-right:0;
    margin-left:-15px;
    min-width: 55px;
    text-align: center;
    height:40px;
  }

  .content__item:first-child {
    border-top:1px solid $color__border;
  }
</style>
