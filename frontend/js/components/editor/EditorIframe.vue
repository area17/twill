<template>
  <div class="editorIframe" >
    <iframe :srcdoc="preview" ref="frame" @load="loadPreview"></iframe>
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
        console.log('preview')
        console.log(this.block.id)
        return this.previewsById(this.block.id) || 'No preview'
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
        console.log(frame)
        frameBody.style.overflow = 'hidden'

        console.log('Refresh the iframe')
        frame.height = frameBody.scrollHeight + 'px'
      },
      _resize: debounce(function () {
        console.log('Resize the iframe')
        this.refresh()
      }, 200),
      loadPreview: function (event) {
        console.log('loadPreview')
        console.log(event)
        this.refresh()
      }
    },
    mounted: function () {
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
