<template>
  <div class="editorIframe" >
    <iframe :srcdoc="preview" ref="frame" @load="loadedPreview"></iframe>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  import debounce from 'lodash/debounce'

  export default {
    name: 'A17editoriframe',
    props: {
      block: {
        type: Object,
        default: function () {
          return {}
        }
      }
    },
    computed: {
      preview: function () {
        return this.previewsById(this.block.id) || ''
      },
      ...mapGetters([
        'previewsById'
      ]),
      ...mapState({
        activeBlock: state => state.content.active,
        savedBlocks: state => state.content.blocks
      })
    },
    methods: {
      refresh: function () {
        const frame = this.$refs.frame
        const frameBody = frame.contentWindow.document.body

        // no scollbars
        frameBody.style.overflow = 'hidden'

        // get body extra margin
        const bodyStyle = window.getComputedStyle(frameBody)
        const bodyMarginTop = bodyStyle.getPropertyValue('margin-top')
        const bodyMarginBottom = bodyStyle.getPropertyValue('margin-bottom')

        const frameHeight = frameBody.scrollHeight + parseInt(bodyMarginTop) + parseInt(bodyMarginBottom)

        console.log('Iframe - Change the iframe height : ' + frameHeight + 'px')
        frame.height = frameHeight + 'px'
      },
      _resize: debounce(function () {
        console.log('Iframe - Resize Preview')
        this.refresh()
      }, 200),
      loadedPreview: function (event) {
        console.log('Iframe - Loaded Preview')
        this.refresh()
      },
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

  .editorIframe {
    cursor:pointer;

    iframe {
      width: 100%;
      overflow: hidden;
      display: block;
    }
  }
</style>
