<template>
  <div class="editorPreview">
    <div class="editorPreview__empty" v-if="!blocks.length">
      <b>Add content</b>
    </div>
    <draggable class="editorPreview__content" v-model="blocks" :options="{ group: 'editorBlocks', handle: handle }" @add="onAdd" @update="onUpdate">
      <div class="editorPreview__item" :class="{ 'editorPreview__item--active' : isBlockActive(block.id) }" v-for="(block, index) in blocks" :key="block.id" >
        <div class="editorPreview__frame" tabindex="0" @click="selectBlock(index)">
          <a17-editor-iframe :block="block" @loaded="resizeIframe"></a17-editor-iframe>
        </div>
        <div class="editorPreview__protector" @click="selectBlock(index)"></div>
        <div class="editorPreview__actions">
          <a17-buttonbar >
            <button type="button" @click="selectBlock(index)"><span v-svg symbol="edit_large"></span></button>
            <button type="button" class="editorPreview__handle"><span v-svg symbol="drag"></span></button>
            <button type="button" @click="deleteBlock(index)"><span v-svg symbol="trash"></span></button>
          </a17-buttonbar>
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
        handle: '.editorPreview__handle' // Drag handle override
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
      _resize: debounce(function () {
        let self = this
        const iframes = this.$el.querySelectorAll('iframe')

        iframes.forEach(function (iframe) {
          self.resizeIframe(iframe)
        })
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
  }

  .editorPreview__content {
    position:absolute;
    top:0;
    bottom:0;
    right:0;
    left:0;
    padding:20px;
    overflow-y: scroll;
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

  .editorPreview__actions {
    display:none;
  }

  .editorPreview__item {
    min-height:40px + 20px + 20px;
    border:1px dashed $color__background;
    border-radius:2px;
    position:relative;
  }

  .editorPreview__item--active {
    border-color:$color__text;
  }

  .editorPreview__item:hover,
  .editorPreview__item--active  {
    .editorPreview__actions {
      display:block;
    }
  }

  .editorPreview__protector {
    position:absolute;
    left:0;
    right:0;
    top:0;
    bottom:0;
    cursor:pointer;
  }

  .editorPreview__actions {
    position:absolute;
    right:20px;
    top:20px;
  }
</style>
