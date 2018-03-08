<template>
  <div class="editorIframe" >
    <div class="editorIframe__empty" v-if="preview === ''">
      {{ title }}
    </div>
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
      title: function () {
        return this.block.title || ''
      },
      ...mapGetters([
        'previewsById'
      ]),
      ...mapState({
        savedBlocks: state => state.content.blocks
      })
    },
    methods: {
      loadedPreview: function (event) {
        if (this.$refs.frame && this.$refs.frame.srcdoc) this.$emit('loaded', this.$refs.frame)
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

  .editorIframe__empty {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    text-align:center;
    display: flex;
    flex-wrap:no-wrap;
    align-items: center;
    justify-content: center;
    color:rgba($color__text, 0.5);
    background-color:rgba($color_editor--active, 0.05);
    border:1px solid rgba($color_editor--active, 0.33);
  }

  .editor__preview--dark .editorIframe__empty {
    color:rgba($color__background, 0.75);
    background-color:rgba($color_editor--active, 0.2);
    border:1px solid rgba($color_editor--active, 0.5);
  }
</style>
