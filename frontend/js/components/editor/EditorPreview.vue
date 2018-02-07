<template>
  <div class="editorPreview" @mousedown="unselectBlock">
    <div class="editorPreview__empty" v-if="!blocks.length">
      <b>Add content</b>
    </div>
    <draggable class="editorPreview__content" v-model="blocks" :options="{ group: 'editorBlocks', handle: handle }" @add="onAdd" @update="onUpdate">
      <div class="editorPreview__item" :class="{ 'editorPreview__item--active' : isBlockActive(block.id) }" v-for="(block, index) in blocks" :key="block.id" @mousedown.stop >
        <div class="editorPreview__frame" tabindex="0" @click="selectBlock(index)">
          <a17-editor-iframe :block="block" @loaded="resizeIframe"></a17-editor-iframe>
        </div>
        <div class="editorPreview__protector editorPreview__dragger" @click="selectBlock(index)"></div>
        <div class="editorPreview__header">
          <div class="editorPreview__actions">
            <a17-buttonbar>
              <button type="button" class="editorPreview__dragger"><span v-svg symbol="drag"></span></button>
              <button type="button" @click="deleteBlock(index)"><span v-svg symbol="trash"></span></button>
            </a17-buttonbar>
          </div>
        </div>
      </div>
    </draggable>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import draggableMixin from '@/mixins/draggable'
  import EditorIframe from './EditorIframe.vue'
  import draggable from 'vuedraggable'

  import debounce from 'lodash/debounce'

  export default {
    name: 'A17editorpreview',
    components: {
      draggable,
      'a17-editor-iframe': EditorIframe
    },
    mixins: [draggableMixin],
    data: function () {
      return {
        handle: '.editorPreview__dragger' // Drag handle override
      }
    },
    computed: {
      blocks: {
        get () {
          return this.savedBlocks
        },
        set (value) {
        }
      },
      hasBlockActive: function () {
        return Object.keys(this.activeBlock).length
      },
      ...mapState({
        activeBlock: state => state.content.active,
        savedBlocks: state => state.content.blocks
      })
    },
    methods: {
      onAdd: function (evt) {
        const item = evt.item
        const block = {}

        block.title = item.getAttribute('data-title')
        block.component = item.getAttribute('data-component')
        block.icon = item.getAttribute('data-icon')

        this.addBlock(block, Math.max(0, evt.newIndex))
      },
      onUpdate: function (evt) {
        this.$store.commit('moveBlock', {
          oldIndex: evt.oldIndex,
          newIndex: evt.newIndex
        })
      },
      isBlockActive: function (id) {
        if (!this.hasBlockActive) return false

        return id === this.activeBlock.id
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

        this.$emit('add', fromIndex)
      },
      deleteBlock: function (index) {
        this.$emit('delete', index)
      },
      selectBlock: function (index) {
        this.$emit('select', index)
      },
      unselectBlock: function () {
        this.$emit('unselect')
      },
      resizeIframe: function (iframe) {
        const frameBody = iframe.contentWindow.document.body

        // no scollbars
        frameBody.style.overflow = 'hidden'

        // get body extra margin
        const bodyStyle = window.getComputedStyle(frameBody)
        const bodyMarginTop = bodyStyle.getPropertyValue('margin-top')
        const bodyMarginBottom = bodyStyle.getPropertyValue('margin-bottom')
        const frameHeight = frameBody.scrollHeight + parseInt(bodyMarginTop) + parseInt(bodyMarginBottom)

        console.log('Editor - Preview refresh height : ' + frameHeight + 'px')
        iframe.height = frameHeight + 'px'
      },
      resizeAllIframes: function () {
        let self = this
        const iframes = this.$el.querySelectorAll('iframe')

        iframes.forEach(function (iframe) {
          self.resizeIframe(iframe)
        })
      },
      _resize: debounce(function () {
        this.resizeAllIframes()
      }, 200),
      init: function () {
        window.addEventListener('resize', this._resize)
      },
      dispose: function () {
        window.removeEventListener('resize', this._resize)
      }
    },
    mounted: function () {
      this.init()
    },
    beforeDestroy: function () {
      this.dispose()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .editorPreview {
    background-color:inherit;
  }

  .editorPreview__content {
    position:absolute;
    top:0;
    bottom:0;
    right:0;
    left:0;
    padding:20px;
    overflow-y: scroll;
    background-color:inherit;
  }

  .editorPreview__empty {
    position:absolute;
    top:0;
    bottom:0;
    right:0;
    left:0;
    display:flex;
    align-items: center;
    justify-content: center;
    color:$color__fborder;
    background-color:inherit;

    &::after {
      display:block;
      content:'';
      position:absolute;
      top:20px;
      bottom:20px;
      right:20px;
      left:20px;
      border:1px dashed $color__fborder;
    }
  }

  .editorPreview__empty + .editorPreview__content {
    background-color: transparent;
  }

  .editorPreview__item {
    min-height:80px;
    position:relative;
    margin-bottom:1px;

    &::after {
      content:'';
      border-radius:2px;
      position:absolute;
      top:0;
      right:0;
      left:0;
      bottom:0;
      border:1px solid $color__border;
      z-index:0;
      opacity:0;
    }
  }

  .editorPreview__item:hover::after {
    border-color:$color__border;
    opacity:1;
  }

  .editorPreview__item--active::after,
  .editorPreview__item--active:hover::after {
    border-color:$color_editor--active;
    opacity:1;
  }

  .editorPreview__protector {
    position:absolute;
    left:0;
    right:0;
    top:0;
    bottom:0;
    cursor:move;
    z-index:1;
  }

  .editorPreview__header {
    position:absolute;
    top:20px;
    right:20px;
    padding:0;
    display:none;
    background-clip: padding-box;
    z-index:2;
  }

  .editorPreview__handle {
    position:absolute;
    height:10px;
    width:40px;
    left:50%;
    top:50%;
    margin-left:-20px;
    margin-top:-5px;
    @include dragGrid($color__drag, $color__block-bg);
  }

  .editorPreview__item:hover .editorPreview__header,
  .editorPreview__item--active .editorPreview__header {
    display:flex;
  }
</style>
