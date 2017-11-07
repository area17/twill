<template>
  <div class="content">
    <draggable class="content__content" v-model="blocks" :options="dragOptions">
      <transition-group name="draggable_list" tag='div'>
        <div class="content__item" v-for="(block, index) in blocks" :key="block.id">
          <a17-block :block="block" :index="index">
            <button type="button" slot="dropdown-add" v-if="availableBlocks.length" v-for="(availableBlock, dropdownIndex) in availableBlocks" :key="availableBlock.component" @click="addBlock(availableBlock, index)"><span v-svg :symbol="availableBlock.icon"></span> {{ availableBlock.title }}</button>
            <div slot="dropdown-action">
              <!--TBD: Maybe this could be delete-->
              <!-- <button type="button" @click="deleteBlock(index)">Open in Live Editor</button> -->
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
        <button type="button" v-for="(availableBlock, index) in availableBlocks" :key="availableBlock.component" @click="addBlock(availableBlock, -1)"><span v-svg :symbol="availableBlock.icon"></span> {{ availableBlock.title }}</button>
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
      addDropdownId: function (index) {
        return `addBlock${index}Dropdown`
      },
      toggleDropdown: function (index) {
        const dropdownId = this.addDropdownId(index)
        const dropdown = this.$refs[dropdownId][0]
        if (dropdown) dropdown.toggle()
      },
      addBlock: function (block, fromIndex) {
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
        this.$store.commit('duplicateBlock', index)
      },
      deleteBlock: function (index) {
        this.$store.commit('deleteBlock', index)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../scss/setup/variables.scss";
  @import "../../scss/setup/colors.scss";
  @import "../../scss/setup/mixins.scss";

  .content {
    margin-top:35px;
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

  .content__item:first-child {
    border-top:1px solid $color__border;
  }
</style>
