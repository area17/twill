<template>
  <div class="editorIframe" >
    <iframe :srcdoc="preview" ref="frame" @load="loadedPreview"></iframe>
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
      loadedPreview: function (event) {
        if (this.$refs.frame) this.$emit('loaded', this.$refs.frame)
      }
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
