<template>
  <div class="content">
    <draggable class="content__content" v-model="blocks" :options="dragOptions">
      <transition-group name="draggable_list" tag='div'>
        <div class="content__item" v-for="(block, index) in blocks" :key="block.id">
          <a17-block :block="block" :index="index" :opened="opened" @open="setOpened">
            <button type="button" slot="dropdown-add" v-if="availableBlocks.length" v-for="(availableBlock, dropdownIndex) in availableBlocks" :key="availableBlock.component" @click="addBlock(availableBlock, index)"><span v-svg :symbol="availableBlock.icon"></span> {{ availableBlock.title }}</button>
            <div slot="dropdown-action">
              <button type="button" @click="collapseAllBlocks()">Collapse All</button>
              <!-- <button type="button" @click="">Open in Live Editor</button> -->
              <button type="button" @click="deleteBlock(index)">Delete</button>
              <button type="button" @click="duplicateBlock(index)">Duplicate</button>
            </div>
          </a17-block>
        </div>
      </transition-group>
    </draggable>

    <a17-dropdown ref="blocksDropdown" position="top-center" :arrow="true" :offset="10" v-if="availableBlocks.length">
      <a17-button size="small" variant="action" @click="$refs.blocksDropdown.toggle()">{{ title }}</a17-button>
      <div slot="dropdown__content">
        <button type="button" v-for="(availableBlock, index) in availableBlocks" :key="availableBlock.component" @click="addBlock(availableBlock, -1)"><span class="content__icon" v-svg :symbol="availableBlock.icon"></span> {{ availableBlock.title }}</button>
      </div>
    </a17-dropdown>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import draggable from 'vuedraggable'
  import draggableMixin from '@/mixins/draggable'
  import Block from '@/components/blocks/Block.vue'

  export default {
    name: 'A17Content',
    components: {
      'a17-block': Block,
      draggable
    },
    mixins: [draggableMixin],
    props: {
      title: {
        type: String,
        default: ''
      }
    },
    data: function () {
      return {
        opened: true,
        handle: '.block__handle' // drag handle
      }
    },
    computed: {
      blocks: {
        get () {
          return this.savedBlocks
        },
        set (value) {
          this.$store.commit('reorderBlocks', value)
        }
      },
      ...mapState({
        savedBlocks: state => state.content.blocks,
        availableBlocks: state => state.content.available
      })
    },
    methods: {
      setOpened: function (value) {
        this.opened = value
      },
      addDropdownId: function (index) {
        return `addBlock${index}Dropdown`
      },
      toggleDropdown: function (index) {
        const dropdownId = this.addDropdownId(index)
        const dropdown = this.$refs[dropdownId][0]
        if (dropdown) dropdown.toggle()
      },
      addBlock: function (block, fromIndex) {
        this.opened = true
        let newBlock = {
          title: block.title,
          type: block.component,
          icon: block.icon,
          attributes: block.attributes
        }

        this.$store.commit('addBlock', {
          block: newBlock,
          index: fromIndex
        })
      },
      duplicateBlock: function (index) {
        this.opened = true
        this.$store.commit('duplicateBlock', index)
      },
      deleteBlock: function (index) {
        this.$store.commit('deleteBlock', index)
      },
      collapseAllBlocks: function () {
        this.opened = false
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

  .content__content {
    margin-bottom:20px;

    + .dropdown {
      display:inline-block;
    }
  }

  .content__item {
    border:1px solid $color__border;
    border-top:0 none;

    &.sortable-ghost {
      opacity:0.5;
    }
  }

  .content__icon {
    min-width: 19px;
    text-align: center;
  }

  .content__item:first-child {
    border-top:1px solid $color__border;
  }
</style>
