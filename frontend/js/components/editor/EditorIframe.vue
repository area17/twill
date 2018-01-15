<template>
  <div class="editorIframe" >
    <iframe :srcdoc="preview" @load="loadPreview"></iframe>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

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
      loadPreview: function () {
        console.log('loadPreview')
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
