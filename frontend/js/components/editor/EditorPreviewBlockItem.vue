<template>
  <div class="editorPreview__item"
       :class="previewBlockItemClasses"
       @mousedown.stop>

    <div class="editorPreview__frame">
      <a17-editor-iframe :block="block"
                         @loaded="iframeLoaded"
                         ref="blockIframe"/>
    </div>
    <div class="editorPreview__protector editorPreview__dragger"
         @click.prevent="handleBlockPreviewClick">
    </div>
    <div class="editorPreview__header">
      <a17-buttonbar variant="visible">
        <a17-dropdown v-if="blocksLength > 1"
                      class="f--small"
                      position="bottom-left"
                      :maxHeight="270"
                      ref="blockDropdown"
                      @open="handleDropDownOpen"
                      @close="handleDropDownClose">
          <button type="button"
                  @click="toggleBlockDropdown(blockIndex)">
                <span v-svg
                      symbol="drag"></span>
          </button>
          <div slot="dropdown__content">
            <button type="button"
                    v-for="n in blocksLength"
                    @click="moveBlock(n - 1)"
                    :key="n">
              {{ n }}
            </button>
          </div>
        </a17-dropdown>
        <button type="button"
                @click="deleteBlock">
              <span v-svg
                    symbol="trash"></span>
        </button>
      </a17-buttonbar>
    </div>
  </div>

</template>

<script>
  import EditorIframe from '@/components/editor/EditorIframe.vue'
  import { BlockItemMixin } from '@/mixins'

  export default {
    name: 'A17EditorPreviewBlockItem',
    props: {
      isBlockActive: {
        type: Boolean,
        default: false
      }
    },
    mixins: [BlockItemMixin],
    components: {
      'a17-editor-iframe': EditorIframe
    },
    data () {
      return {
        dropdownOpen: false
      }
    },
    computed: {
      previewBlockItemClasses () {
        return {
          'editorPreview__item--active': this.isBlockActive,
          'editorPreview__item--dropdown-open': this.dropdownOpen
        }
      }
    },
    methods: {
      handleBlockPreviewClick () {
        if (this.isBlockActive) {
          this.unselectBlock()
        } else {
          this.selectBlock()
        }
      },
      handleDropDownOpen () {
        this.dropdownOpen = true
      },
      handleDropDownClose () {
        this.dropdownOpen = false
      },
      iframeLoaded () {
        if (!this.isBlockActive) return
        this.$nextTick(() => {
          this.$emit('scroll-to', this.$el.offsetTop)
        })
      }
    },
    beforeDestroy () {
      this.unselectBlock()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .editorPreview__item {
    min-height: 80px;
    position: relative;
    margin-bottom: 1px;
    z-index: 1;

    &::after {
      content: '';
      border-radius: 2px;
      position: absolute;
      top: 0;
      right: 0;
      left: 0;
      bottom: 0;
      border: 1px solid $color__border;
      z-index: 0;
      opacity: 0;
    }
  }

  .editorPreview__item:hover::after {
    border-color: $color__border;
    opacity: 1;
  }

  .editorPreview__item--dropdown-open {
    z-index: 2;
  }

  .editorPreview__item--active::after,
  .editorPreview__item--active:hover::after {
    border-color: $color_editor--active;
    opacity: 1;
  }

  .editorPreview__protector {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    cursor: move;
    z-index: 1;
  }

  .editorPreview__header {
    position: absolute;
    top: 20px;
    right: 20px;
    padding: 0;
    display: none;
    background-clip: padding-box;
    z-index: 2;
  }

  .editorPreview__item:hover .editorPreview__header,
  .editorPreview__item--active .editorPreview__header,
  .editorPreview__item--dropdown-open .editorPreview__header {
    display: flex;
  }

  /* Dragged item */
  .editorPreview__item.sortable-chosen {
    opacity: 1;
  }

  .editorPreview__item.sortable-ghost {
    opacity: 0.25;
  }
</style>
