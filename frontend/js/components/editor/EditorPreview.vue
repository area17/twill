<template>
  <a17-block-model :section="section">
    <div class="editorPreview"
         :class="previewClass"
         :style="previewStyle"
         slot-scope="{ add, edit, unEdit }"
         @mousedown="_unselectBlock(unEdit)">
      <div class="editorPreview__empty"
           v-if="!blocks.length">
        <b>{{ $trans('editor.preview-empty') }}</b>
      </div>
      <draggable class="editorPreview__content"
                 ref="previewContent"
                 :value="blocks"
                 :options="{ group: 'editorBlocks', handle: handle }"
                 @add="onAdd(add, edit, $event)"
                 @update="onUpdate">
        <template v-for="savedBlock in blocks">
          <a17-block-model :block="savedBlock"
                           :key="savedBlock.id"
                           :section="section">
            <a17-editor-block-preview slot-scope="{ block, isActive, blockIndex, move, remove, edit, unEdit }"
                                      :ref="block.id"
                                      :block="block"
                                      :blockIndex="blockIndex"
                                      :blocksLength="blocks.length"
                                      :isBlockActive="isActive"
                                      :key="savedBlock.id"
                                      @block:select="_selectBlock(edit, blockIndex)"
                                      @block:unselect="_unselectBlock(unEdit, blockIndex)"
                                      @block:move="move"
                                      @block:delete="deleteBlock(remove)"
                                      @scroll-to="scrollToActive"/>
          </a17-block-model>
        </template>
      </draggable>
      <a17-spinner v-if="loading"
                   :visible="true">{{ $trans('fields.block-editor.loading', 'Loading') }}&hellip;
      </a17-spinner>
    </div>
  </a17-block-model>
</template>

<script>
  import { DraggableMixin, BlockEditorMixin } from '@/mixins'

  import A17EditorBlockPreview from '@/components/editor/EditorPreviewBlockItem'
  import A17BlockModel from '@/components/blocks/BlockModel'
  import A17Spinner from '@/components/Spinner.vue'

  import { PREVIEW } from '@/store/mutations/index'
  import ACTIONS from '@/store/actions/index'

  import draggable from 'vuedraggable'
  import tinyColor from 'tinycolor2'

  import debounce from 'lodash/debounce'

  export default {
    name: 'A17editorpreview',
    props: {
      bgColor: {
        type: String,
        default: '#FFFFFF'
      },
      hasBlockActive: {
        props: {
          type: Boolean,
          default: false
        }
      }
    },
    mixins: [DraggableMixin, BlockEditorMixin],
    components: {
      draggable,
      'a17-editor-block-preview': A17EditorBlockPreview,
      'a17-block-model': A17BlockModel,
      'a17-spinner': A17Spinner
    },
    data () {
      return {
        loading: false,
        blockSelectIndex: -1,
        unSubscribe: function () {
          return null
        },
        handle: '.editorPreview__dragger' // Drag handle override
      }
    },
    computed: {
      previewClass () {
        const bgColorObj = tinyColor(this.bgColor)
        return {
          'editorPreview--dark': bgColorObj.getBrightness() < 180,
          'editorPreview--loading': this.loading
        }
      },
      previewStyle () {
        return { 'background-color': this.bgColor }
      }
    },
    methods: {
      // blocks management
      onAdd (add, edit, evt) {
        const { item } = evt
        const block = {}

        block.title = item.getAttribute('data-title')
        block.component = item.getAttribute('data-component')
        block.icon = item.getAttribute('data-icon')

        const index = Math.max(0, evt.newIndex)
        this.addAndEditBlock(add, edit, {
          block,
          index: index
        })

        this._selectBlock(null, index)
      },
      onUpdate ({ oldIndex, newIndex }) {
        this.$emit('blocks:move', {
          oldIndex,
          newIndex
        })
      },
      _selectBlock (fn = null, index) {
        if (fn) {
          this.selectBlock(fn, index)
        }
        if (this.blockSelectIndex !== index) {
          this.blockSelectIndex = index
          this.unSubscribe = this.$store.subscribe((mutation) => {
            // console.log('mutation', mutation)
            // Don't trigger a refresh of the preview every single time, just when necessary
            if (PREVIEW.REFRESH_BLOCK_PREVIEW.includes(mutation.type)) {
              // console.log('Editor - store changed : ' + mutation.type)
              if (PREVIEW.REFRESH_BLOCK_PREVIEW_ALL.includes(mutation.type)) {
                this.getAllPreviews()
              } else {
                this.getPreview(index)
              }
            }
          })
        }
      },
      _unselectBlock (fn, index = this.blockSelectIndex) {
        this.getPreview(index)
        this.unselectBlock(fn, index)
        this.blockSelectIndex = -1
        this.unSubscribe()
      },

      // Previews management
      getAllPreviews () {
        this.loading = true
        this.$store.dispatch(ACTIONS.GET_ALL_PREVIEWS, {
          section: this.section
        })
          .then(() => {
            this.$nextTick(() => {
              this.loading = false
            })
          })
      },
      getPreview (index = -1) {
        this.loading = true
        this.$store.dispatch(ACTIONS.GET_PREVIEW, {
          section: this.section,
          index: index
        })
          .then(() => {
            this.$nextTick(() => {
              this.loading = false
            })
          })
      },

      // UI Management
      scrollToActive (target) {
        this.$refs.previewContent.$el.scrollTop = Math.max(0, target - 20)
      },
      resizeAllIframes () {
        if (!this.$refs.blockPreview) return
        this.$refs.blockPreview.forEach(preview => {
          preview.$refs.blockIframe.resize()
        })
      },
      _resize: debounce(function () {
        this.resizeAllIframes()
      }, 200),
      init () {
        window.addEventListener('resize', this._resize)
      },
      dispose () {
        window.removeEventListener('resize', this._resize)
      }
    },
    mounted () {
      this.init()
      this.$nextTick(() => {
        this.getAllPreviews()
      })
    },
    beforeDestroy () {
      this.dispose()
    },
    watch: {
      section () {
        this.unSubscribe()
        this.getAllPreviews()
      },
      hasBlockActive (active) {
        if (active) return
        this.unSubscribe()
        this.blockSelectIndex = -1
      }
    }
  }
</script>

<style lang="scss" scoped>

  .editorPreview {
    background-color: inherit;
    color: inherit;
  }

  .editorPreview__content {
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;
    left: 0;
    padding: 20px;
    overflow-y: auto;
    background-color: inherit;
  }

  .editorPreview__empty {
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: inherit;
    background-color: inherit;

    &::after {
      display: block;
      content: '';
      position: absolute;
      top: 20px;
      bottom: 20px;
      right: 20px;
      left: 20px;
      border: 1px dashed $color__fborder;
    }

    > * {
      padding: 0 40px;
      @include font-medium;
      line-height: 1.35em;
      text-align: center;
      font-weight: 400;
    }
  }

  .editorPreview__empty + .editorPreview__content {
    background-color: transparent;
  }

  .editorPreview__handle {
    position: absolute;
    height: 10px;
    width: 40px;
    left: 50%;
    top: 50%;
    margin-left: -20px;
    margin-top: -5px;
    @include dragGrid($color__drag, $color__block-bg);
  }
</style>
