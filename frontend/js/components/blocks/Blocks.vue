<template>
  <a17-blocks-list :editor-name="editorName" v-slot="{ savedBlocks, availableBlocks, moveBlock }">
    <div class="blocks">
      <draggable class="blocks__container"
                 :value="savedBlocks"
                 @update="({oldIndex, newIndex}) => moveBlock({ oldIndex, newIndex })"
                 :options="dragOptions">
        <transition-group name="draggable_list"
                          tag='div'>
          <div class="blocks__item"
               v-for="savedBlock in savedBlocks"
               :key="savedBlock.id">
            <a17-block-model :editor-name="editorName"
                             :block="savedBlock"
                             v-slot="{ block, blockIndex, add, edit, move, remove, duplicate }">
              <a17-block-item ref="blockList"
                              :block="block"
                              :index="blockIndex"
                              :opened="opened"
                              @expand="setOpened"
                              v-if="availableBlocks.length">
                <template v-for="availableBlock in availableBlocks">
                  <button
                    type="button"
                    slot="dropdown-add"
                    :key="availableBlock.component"
                    @click="handleBlockAdd(add, availableBlock, blockIndex + 1)">
                    <span
                      v-svg
                      :symbol="availableBlock.icon"></span> {{ availableBlock.title }}
                    </button>
                </template>
                <div slot="dropdown-action">
                  <button type="button"
                          v-if="opened"
                          @click="collapseAllBlocks()">
                          {{ $trans('fields.block-editor.collapse-all', 'Collapse all') }}
                  </button>
                  <button type="button"
                          v-else
                          @click="expandAllBlocks()">
                          {{ $trans('fields.block-editor.expand-all', 'Expand all') }}
                  </button>
                  <button type="button"
                          v-if="editor"
                          @click="openInEditor(edit, blockIndex, editorName)">
                          {{ $trans('fields.block-editor.open-in-editor', 'Open in editor') }}
                  </button>
                  <button type="button"
                          @click="handleDuplicateBlock(duplicate)">
                          {{ $trans('fields.block-editor.create-another', 'Create another') }}
                  </button>
                  <button type="button"
                          @click="handleDeleteBlock(remove)">
                          {{ $trans('fields.block-editor.delete', 'Delete') }}
                  </button>
                </div>
                <button slot="dropdown-numbers"
                        type="button"
                        v-for="n in savedBlocks.length"
                        @click="move(n - 1)"
                        :key="n">{{ n }}
                </button>
              </a17-block-item>
            </a17-block-model>
          </div>
        </transition-group>
      </draggable>

      <div class="blocks__actions">
        <a17-dropdown ref="blocksDropdown"
                      position="top-center"
                      v-if="availableBlocks.length"
                      :arrow="true"
                      :offset="10"
                      :maxHeight="430">

          <a17-button size="small"
                      variant="action"
                      @click="$refs.blocksDropdown.toggle()">{{ title }}
          </a17-button>

          <div slot="dropdown__content">
            <template v-for="availableBlock in availableBlocks">
              <a17-block-model :editor-name="editorName"
                               :block="availableBlock"
                               :key="availableBlock.component"
                               v-slot="{ add, block }">
                <button type="button"
                        :key="availableBlock.component"
                        @click="handleBlockAdd(add, block)">
                  <span class="blocks__icon"
                        v-svg
                        :symbol="availableBlock.icon"></span>
                  {{ availableBlock.title }}
                </button>
              </a17-block-model>
            </template>
          </div>
        </a17-dropdown>
        <div class="blocks__secondaryActions">
          <a href="#"
             class="f--link f--link-underlined--o"
             v-if="editor"
             @click.prevent="openEditor(-1, editorName)">
            {{ $trans('fields.block-editor.open-in-editor', 'Open in editor') }}
          </a>
        </div>
      </div>
    </div>
  </a17-blocks-list>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { DraggableMixin, EditorMixin } from '@/mixins/index'
  import draggable from 'vuedraggable'
  import BlockItem from '@/components/blocks/BlockItem.vue'
  import BlocksList from '@/components/blocks/BlocksList'
  import BlockModel from '@/components/blocks/BlockModel'

  export default {
    name: 'A17Blocks',
    components: {
      'a17-block-item': BlockItem,
      'a17-block-model': BlockModel,
      'a17-blocks-list': BlocksList,
      draggable
    },
    mixins: [DraggableMixin, EditorMixin],
    props: {
      title: {
        type: String,
        default: ''
      },
      editorName: {
        type: String,
        required: true
      }
    },
    data () {
      return {
        opened: true,
        handle: '.block__handle' // drag handle
      }
    },
    computed: {
      ...mapState({
        editor: state => state.blocks.editor
      }),
      ...mapGetters([
        'blocks'
      ])
    },
    methods: {
      setOpened: function () {
        const allClosed = this.$refs.blockList && this.$refs.blockList.every((block) => !block.visible)
        if (allClosed) {
          this.opened = false
        }
      },
      collapseAllBlocks: function () {
        this.opened = false
      },
      expandAllBlocks: function () {
        this.opened = true
      },
      checkExpandBlocks () {
        this.allBlocksExpands = this.$refs.blockList.every((blocks) => blocks.visible)
      },
      handleBlockAdd (fn, block, index = -1) {
        fn(block, index)
        this.$nextTick(() => {
          this.checkExpandBlocks()
        })
      },
      handleDuplicateBlock (fn, index) {
        fn(index)
        this.$nextTick(() => {
          this.checkExpandBlocks()
        })
      },
      handleDeleteBlock (fn, index) {
        // open confirm dialog if any
        if (this.$root.$refs.warningContentEditor) {
          this.$root.$refs.warningContentEditor.open(() => {
            fn(index)
            this.$nextTick(() => {
              this.checkExpandBlocks()
            })
          })
        } else {
          fn(index)
          this.$nextTick(() => {
            this.checkExpandBlocks()
          })
        }
      },
      openInEditor (fn, block, index) {
        fn()
        this.openEditor(block, index)
      }
    },
    mounted () {
      // if there are blocks, these should be all collapse by default
      this.$nextTick(function () {
        if (this.blocks(this.editorName) && this.blocks(this.editorName).length > 3) this.collapseAllBlocks()
      })
    }
  }
</script>

<style lang="scss" scoped>
  .blocks {
    margin-top: 20px; // margin-top:35px;
  }

  .blocks__container {
    margin-bottom: 20px;

    + .dropdown {
      display: inline-block;
    }
  }

  .blocks__actions {
    display: flex;
  }

  .blocks__secondaryActions {
    flex-grow: 1;
    text-align: right;
    margin-left: 20px;
    padding-top: 8px;
  }

  .blocks__item {
    border: 1px solid $color__border;
    border-top: 0 none;

    &.sortable-ghost {
      opacity: 0.5;
    }
  }

  .blocks__actions button .blocks__icon {
    margin-right: 0;
    margin-left: -15px;
    min-width: 55px;
    text-align: center;
    height: 40px;
  }

  .blocks__item:first-child {
    border-top: 1px solid $color__border;
  }
</style>
