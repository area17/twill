<template>
  <a17-blocks-list :editor-name="editorName" :availability-id="availabilityId" v-slot="{ savedBlocks, availableBlocks, moveBlock, moveBlockToEditor, cloneBlock }">
    <div class="blocks">
      <draggable class="blocks__container"
                 :value="savedBlocks"
                 group="blocks"
                 :move="handleOnMove"
                 @end="handleOnEnd(moveBlock, moveBlockToEditor)"
                 v-bind="dragOptions">
        <transition-group name="draggable_list"
                          tag='div'>
          <div class="blocks__item"
               v-for="savedBlock in savedBlocks"
               :key="savedBlock.id">
            <a17-blockeditor-model :editor-name="editorName"
                             :block="savedBlock"
                             v-slot="{ block, blockIndex, add, edit, move, remove, duplicate }">
              <a17-blockeditor-item ref="blockList"
                              :block="block"
                              :index="blockIndex"
                              :opened="opened"
                              :with-handle="!isSettings"
                              :with-actions="!isSettings"
                              @expand="setOpened"
                              v-if="availableBlocks.length">
                <template v-for="availableBlock in availableBlocks">
                  <button
                    class="blocks__addButton"
                    type="button"
                    slot="dropdown-add"
                    :key="availableBlock.component"
                    @click="handleBlockAdd(add, availableBlock, blockIndex + 1)"
                  >
                    <span
                      class="blocks__icon"
                      v-svg
                      :symbol="availableBlock.icon"
                    ></span>
                    <span class="blocks__title">{{ availableBlock.title }}</span>
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
                          v-if="editor && !editorName.includes('|')"
                          @click="openInEditor(edit, blockIndex, editorName)">
                          {{ $trans('fields.block-editor.open-in-editor', 'Open in editor') }}
                  </button>
                  <button type="button"
                          @click="handleClone(cloneBlock, blockIndex, block)">
                          {{ $trans('fields.block-editor.clone-block', 'Clone block') }}
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
              </a17-blockeditor-item>
            </a17-blockeditor-model>
          </div>
        </transition-group>
      </draggable>

      <div class="blocks__actions" v-if="!isSettings">
        <a17-dropdown ref="blocksDropdown"
                      position="top-center"
                      v-if="availableBlocks.length"
                      :arrow="true"
                      :offset="10"
                      :maxHeight="430">

          <a17-button size="small"
                      variant="action"
                      @click="$refs.blocksDropdown.toggle()">{{ trigger }}
          </a17-button>

          <div slot="dropdown__content">
            <template v-for="availableBlock in availableBlocks">
              <a17-blockeditor-model :editor-name="editorName"
                               :block="availableBlock"
                               :key="availableBlock.component"
                               v-slot="{ add, block }">
                <button
                  class="blocks__addButton"
                  type="button"
                  :key="availableBlock.component"
                  @click="handleBlockAdd(add, block)"
                >
                  <span
                    class="blocks__icon"
                    v-svg
                    :symbol="availableBlock.icon"
                  ></span>
                  <span class="blocks__title">{{ availableBlock.title }}</span>
                </button>
              </a17-blockeditor-model>
            </template>
          </div>
        </a17-dropdown>
        <div class="blocks__secondaryActions" v-if="!editorName.includes('|')">
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
  import draggable from 'vuedraggable'
  import { mapGetters,mapState } from 'vuex'

  import BlockEditorItem from '@/components/blocks/BlockEditorItem.vue'
  import BlockEditorModel from '@/components/blocks/BlockEditorModel'
  import BlocksList from '@/components/blocks/BlocksList'
  import { DraggableMixin, EditorMixin } from '@/mixins/index'

  export default {
    name: 'A17Blocks',
    components: {
      'a17-blockeditor-item': BlockEditorItem,
      'a17-blockeditor-model': BlockEditorModel,
      'a17-blocks-list': BlocksList,
      draggable
    },
    mixins: [DraggableMixin, EditorMixin],
    props: {
      trigger: {
        type: String,
        default: ''
      },
      isSettings: {
        type: Boolean,
        required: true
      },
      title: {
        type: String,
        default: ''
      },
      editorName: {
        type: String,
        required: true
      },
      availabilityId: {
        type: String,
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
        editor: state => state.blocks.editor,
        editorNames: state => state.blocks.editorNames

      }),
      ...mapGetters([
        'blocks',
        'availableBlocks'
      ]),
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
        if (this.$refs.blockList[this.$refs.blockList.length - 1] !== undefined) {
          this.$refs.blockList[this.$refs.blockList.length - 1].toggleExpand()
        }
      },
      handleOnMove (e) {
        const { draggedContext, relatedContext } = e
        const { index, element: draggedElement, futureIndex } = draggedContext
        const { element: relatedElement } = relatedContext

        this.nextMove = {
          block: draggedElement,
          editorName: relatedElement.name,
          newIndex: futureIndex,
          index
        }
      },
      handleOnEnd (moveFn, moveBlockToEditorFn) {
        if (!this.nextMove) return
        const {
          block,
          editorName,
          newIndex,
          index
        } = this.nextMove

        if (block.name !== editorName) {
          if (this.checkIfBlockTypeIsAvailable(editorName, block.type)) {
            moveBlockToEditorFn && moveBlockToEditorFn(block, editorName, index, newIndex)
          }
        } else {
          moveFn && moveFn({ oldIndex: index, newIndex })
        }
      },
      handleClone (cloneFn, blockIndex, block) {
        cloneFn && cloneFn({ block, index: blockIndex + 1 })
        this.$nextTick(() => {
          this.checkExpandBlocks()
        })
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
      checkIfBlockTypeIsAvailable (editorName, type) {
        const availableBlocks = this.availableBlocks(editorName)
        const blockTypes = availableBlocks.map(block => block.component)

        return blockTypes.indexOf(type) !== -1
      },
      openInEditor (fn, block, index) {
        fn()
        this.openEditor(block, index)
      }
    },
    mounted () {
      // if there are blocks, these should be all collapse by default
      this.$nextTick(function () {
        if (this.$refs.blockList && this.blocks(this.editorName) && this.blocks(this.editorName).length < 4) {
          this.$refs.blockList.forEach((block) => block.toggleExpand())
        }

        this.setOpened()
      })
    }
  }
</script>

<style lang="scss" scoped>
  .blocks {
    margin-top: 20px;
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

    .blocks:first-child {
      margin-top: 35px;
    }
  }

  .blocks__item:first-child {
    border-top: 1px solid $color__border;
  }

  .blocks__addButton {
    display: flex !important;
    align-items: center;

    .blocks__icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-right: 0;
      margin-left: -15px;
      min-width: 55px;
      width: 55px;
      height: 40px;
    }

    .blocks__title {
      flex-grow: 1;
    }
  }
</style>
