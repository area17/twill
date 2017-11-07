<template>
  <div class="content">
    <draggable class="content__content" v-model="blocks" :options="dragOptions">
      <transition-group name="draggable_list" tag='div'>
        <div class="content__item" v-for="(block, index) in blocks" :key="block.id">
          <a17-block :block="block" :index="index">
            <a17-button slot="block-actions" variant="icon" data-action @click="duplicateBlock(index)"  v-if="hasRemainingBlocks"><span v-svg symbol="add"></span></a17-button>
            <div slot="dropdown-action">
              <button type="button" @click="deleteBlock(index)">Delete</button>
              <button type="button" @click="duplicateBlock(index)" v-if="hasRemainingBlocks">Duplicate</button>
            </div>
          </a17-block>
        </div>
      </transition-group>
    </draggable>

    <a17-button size="small" variant="action" @click="addBlock()" v-if="hasRemainingBlocks">{{ blockType.trigger }}</a17-button>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import draggable from 'vuedraggable'
  import draggableMixin from '@/mixins/draggable'
  import Block from '@/components/blocks/Block.vue'

  export default {
    name: 'A17Repeater',
    components: {
      'a17-block': Block,
      draggable
    },
    mixins: [draggableMixin],
    props: {
      id: {
        type: String,
        required: true
      }
    },
    data: function () {
      return {
        handle: '.block__handle' // drag handle
      }
    },
    computed: {
      hasRemainingBlocks: function () {
        return this.blockType.max > this.blocks.length
      },
      blockType: function () {
        return this.availableBlocks[this.id] ? this.availableBlocks[this.id] : {}
      },
      blocks: {
        get () {
          if (this.savedBlocks.hasOwnProperty(this.id)) {
            return this.savedBlocks[this.id] || []
          } else {
            return []
          }
        },
        set (value) {
          this.$store.commit('reorderFormBlocks', {
            id: this.id,
            blocks: value
          })
        }
      },
      ...mapState({
        savedBlocks: state => state.form.repeaters,
        availableBlocks: state => state.form.availableRepeaters
      })
    },
    methods: {
      addBlock: function () {
        this.$store.commit('addFormBlock', { id: this.id })
      },
      duplicateBlock: function (index) {
        this.$store.commit('duplicateFormBlock', {
          id: this.id,
          index: index
        })
      },
      deleteBlock: function (index) {
        this.$store.commit('deleteFormBlock', {
          id: this.id,
          index: index
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

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
