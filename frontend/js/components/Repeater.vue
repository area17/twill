<template>
  <div class="content">
    <draggable class="content__content" v-model="blocks" v-bind="dragOptions">
      <transition-group name="draggable_list" tag='div'>
        <div class="content__item" v-for="(block, index) in blocks" :key="block.id">
          <a17-blockeditor-item
              ref="blockList"
              :block="block"
              :index="index"
              :withHandle="draggable"
              :size="blockSize"
              :opened="opened"
          >
            <a17-button slot="block-actions" variant="icon" data-action @click="duplicateBlock(index)"
                        v-if="hasRemainingBlocks">
              <span v-svg symbol="add"></span>
            </a17-button>
            <div slot="dropdown-action">
              <button type="button" @click="collapseAllBlocks()" v-if="opened">
                {{ $trans('fields.block-editor.collapse-all', 'Collapse all') }}
              </button>
              <button v-else type="button" @click="expandAllBlocks()">
                {{ $trans('fields.block-editor.expand-all', 'Expand all') }}
              </button>
              <button type="button" @click="duplicateBlock(index)" v-if="hasRemainingBlocks">
                {{ $trans('fields.block-editor.clone-block', 'Clone block') }}
              </button>
              <button type="button" @click="deleteBlock(index)">
                {{ $trans('fields.block-editor.delete', 'Delete') }}
              </button>
            </div>
          </a17-blockeditor-item>
        </div>
      </transition-group>
    </draggable>
    <div class="content__trigger">
      <a17-button
          v-if="hasRemainingBlocks && blockType.trigger && allowCreate"
          :class="triggerClass"
          :variant="triggerVariant"
          @click="addBlock()"
      >
        {{ blockType.trigger }}
      </a17-button>
      <a17-button
          v-if="hasRemainingBlocks && browser"
          :class="triggerClass"
          :variant="triggerVariant"
          @click="openBrowser()"
      >
        {{ blockType.selectTrigger }}
      </a17-button>
      <div class="content__note f--note f--small">
        <slot></slot>
      </div>
    </div>
    <a17-standalone-browser
        v-if="browserIsOpen"
        :endpoint="browser"
        :for-repeater="true"
        @selected="addRepeatersFromSelection"
        ref="localbrowser"
        @close="browserIsOpen = false"
        :max="max"
    />
  </div>
</template>

<script>
  import draggable from 'vuedraggable'
  import { mapState } from 'vuex'

  import BlockEditorItem from '@/components/blocks/BlockEditorItem.vue'
  import A17StandaloneBrowser from "@/components/StandaloneBrowser.vue"
  import draggableMixin from '@/mixins/draggable'
  import { FORM } from '@/store/mutations'
  import ACTIONS from "@/store/actions";

  export default {
    name: 'A17Repeater',
    components: {
      A17StandaloneBrowser,
      'a17-blockeditor-item': BlockEditorItem,
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
      },
      buttonAsLink: {
        type: Boolean,
        default: false
      },
      browser: {
        type: Object,
        required: false,
        default: null
      },
      relation: {
        type: String,
        required: false,
      },
      allowCreate: {
        type: Boolean,
        default: true
      },
      max: {
        type: [Number, null],
        required: false,
        default: null
      }
    },
    data: function () {
      return {
        opened: true,
        browserIsOpen: false,
        handle: '.block__handle' // drag handle
      }
    },
    inject: {inContentEditor: {default: false}},
    computed: {
      triggerVariant: function () {
        if (this.buttonAsLink) {
          return 'aslink'
        }
        return this.inContentEditor ? 'outline' : 'action'
      },
      triggerClass: function () {
        return this.inContentEditor ? 'content__button' : ''
      },
      blockSize: function () {
        return this.inContentEditor ? 'small' : ''
      },
      hasRemainingBlocks: function () {
        let max = null
        if (this.max && this.max > 0) {
          max = this.max
        } else if (this.blockType.hasOwnProperty('max')) {
          max = this.blockType.max
        }
        return !max || (max > this.blocks.length)
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
      setOpened: function () {
        const allClosed = this.$refs.blockList && this.$refs.blockList.every((block) => !block.visible)
        if (allClosed) {
          this.opened = false
        }
      },
      addBlock: function () {
        this.$store.commit(FORM.ADD_FORM_BLOCK, { type: this.type, name: this.name })

        this.$nextTick(() => {
          this.checkExpandBlocks()
        })
      },
      addRepeatersFromSelection (selected) {
        this.$store.commit(FORM.ADD_REPEATER_FROM_SELECTION, {
          type: this.type,
          name: this.name,
          selection: selected,
          relation: this.relation
        })
      },
      duplicateBlock: function (index) {
        this.$store.dispatch(ACTIONS.DUPLICATE_REPEATER, {
          editorName: this.name,
          index,
          futureIndex: index + 1,
          block: this.blocks[index],
          id: Date.now() + Math.floor(Math.random() * 1000)
        })

        this.$nextTick(() => {
          this.checkExpandBlocks()
        })
      },
      deleteBlock: function (index) {
        this.$store.commit(FORM.DELETE_FORM_BLOCK, {
          type: this.type,
          name: this.name,
          index
        })
      },
      collapseAllBlocks: function () {
        this.opened = false
      },
      expandAllBlocks: function () {
        this.opened = true
      },
      checkExpandBlocks () {
        if (this.$refs.blockList[this.$refs.blockList.length - 1] !== undefined) {
          this.$refs.blockList[this.$refs.blockList.length - 1].toggleExpand()
        }
      },
      openBrowser: function () {
        this.browserIsOpen = true
      }
    },
    mounted: function () {
      // if there are blocks, these should be all collapse by default
      this.$nextTick(function () {
        if (this.$refs.blockList && this.blocks && this.blocks.length < 4) {
          this.$refs.blockList.forEach((block) => block.toggleExpand())
        }

        this.setOpened()
      })
    }
  }
</script>

<style lang="scss" scoped>

  .content {
    margin-top: 20px;
  }

  .content:first-child {
    margin-top: 35px;
  }

  .content__content {
    margin-bottom: 20px;

    + .dropdown {
      display: inline-block;
    }
  }

  .content__item {
    border: 1px solid $color__border;
    border-top: 0 none;

    &.sortable-ghost {
      opacity: 0.5;
    }
  }

  .content__item:first-child {
    border-top: 1px solid $color__border;
  }

  .content__trigger {
    display: flex;
  }

  .content__button {
    margin-top: -5px;
  }

  .button--aslink {
    display: block;
    width: 100%;
    text-align: center;
  }

  .content__note {
    flex-grow: 1;
    text-align: right;
  }
</style>
