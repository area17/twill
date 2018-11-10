<template>
  <div class="content">
    <draggable class="content__content" v-model="blocks" :options="dragOptions">
      <transition-group name="draggable_list" tag='div'>
        <div class="content__item" v-for="(block, index) in blocks" :key="block.id">
          <a17-block :block="block" :index="index" :size="blockSize" :opened="opened" @open="setOpened">
            <a17-button slot="block-actions" variant="icon" data-action @click="duplicateBlock(index)"  v-if="hasRemainingBlocks"><span v-svg symbol="add"></span></a17-button>
            <div slot="dropdown-action">
              <button type="button" @click="collapseAllBlocks()">Collapse All</button>
              <button type="button" @click="deleteBlock(index)">Delete</button>
              <button type="button" @click="duplicateBlock(index)" v-if="hasRemainingBlocks">Duplicate</button>
            </div>
          </a17-block>
        </div>
      </transition-group>
    </draggable>
    <div class="content__trigger">
      <a17-button :class="triggerClass" :variant="triggerVariant" :size="triggerSize" @click="addBlock()" v-if="hasRemainingBlocks && blockType.trigger">{{ blockType.trigger }}</a17-button>
      <div class="content__note f--note f--small"><slot></slot></div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { FORM } from '@/store/mutations'

  import draggable from 'vuedraggable'
  import draggableMixin from '@/mixins/draggable'
  import Block from '@/components/blocks/BlockItem.vue'

  export default {
    name: 'A17Repeater',
    components: {
      'a17-block': Block,
      draggable
    },
    mixins: [draggableMixin],
    props: {
      type: {
        type: String,
        required: true
      },
      name: {
        type: String,
        required: true
      }
    },
    data: function () {
      return {
        opened: true,
        handle: '.block__handle' // drag handle
      }
    },
    computed: {
      triggerVariant: function () {
        return this.inContentEditor ? 'aslink' : 'action'
      },
      triggerSize: function () {
        return this.inContentEditor ? 'small' : ''
      },
      triggerClass: function () {
        return this.inContentEditor ? 'content__button' : ''
      },
      blockSize: function () {
        return this.inContentEditor ? 'small' : ''
      },
      inContentEditor: function () {
        return typeof this.$parent.repeaterName !== 'undefined'
      },
      hasRemainingBlocks: function () {
        return !this.blockType.hasOwnProperty('max') || (this.blockType.max > this.blocks.length)
      },
      blockType: function () {
        return this.availableBlocks[this.type] ? this.availableBlocks[this.type] : {}
      },
      blocks: {
        get () {
          if (this.savedBlocks.hasOwnProperty(this.name)) {
            return this.savedBlocks[this.name] || []
          } else {
            return []
          }
        },
        set (value) {
          this.$store.commit(FORM.REORDER_FORM_BLOCKS, {
            type: this.type,
            name: this.name,
            blocks: value
          })
        }
      },
      ...mapState({
        savedBlocks: state => state.repeaters.repeaters,
        availableBlocks: state => state.repeaters.availableRepeaters
      })
    },
    methods: {
      setOpened: function (value) {
        this.opened = value
      },
      addBlock: function () {
        this.opened = true
        this.$store.commit(FORM.ADD_FORM_BLOCK, { type: this.type, name: this.name })
      },
      duplicateBlock: function (index) {
        this.opened = true
        this.$store.commit(FORM.DUPLICATE_FORM_BLOCK, {
          type: this.type,
          name: this.name,
          index: index
        })
      },
      deleteBlock: function (index) {
        this.$store.commit(FORM.DELETE_FORM_BLOCK, {
          type: this.type,
          name: this.name,
          index: index
        })
      },
      collapseAllBlocks: function () {
        this.opened = false
      }
    },
    mounted: function () {
      const self = this
      // if there are blocks, these should be all collapse by default
      this.$nextTick(function () {
        if (self.savedBlocks.length > 0) self.collapseAllBlocks()
      })
    }
  }
</script>

<style lang="scss" scoped>

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

  .content__item:first-child {
    border-top:1px solid $color__border;
  }

  .content__trigger {
    display:flex;
  }

  .content__button {
    display:block;
    width:100%;
    text-align:center;
    margin-top:-5px;
  }

  .content__note {
    flex-grow:1;
    text-align:right;
  }
</style>
