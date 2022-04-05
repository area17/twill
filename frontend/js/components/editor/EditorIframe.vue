<template>
  <div class="editorIframe">
    <div class="editorIframe__empty"
         v-if="preview === ''">
      {{ title }}
    </div>
    <template v-else>
      <iframe v-if="sandbox"
              ref="frame"
              :srcdoc="preview"
              :sandbox="sandboxOptions"
              scrolling="no"
              @load="loadedPreview">
      </iframe>
      <iframe v-else ref="frame"
              :srcdoc="preview"
              scrolling="no"
              @load="loadedPreview">
      </iframe>
    </template>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'

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
      preview () {
        return this.previewsById(this.block.id) || ''
      },
      title () {
        return this.block.title || ''
      },
      sandboxOptions () {
        return typeof this.sandbox === 'boolean' ? 'allow-same-origin allow-top-navigation allow-scripts' : this.sandbox.join(' ')
      },
      ...mapGetters([
        'previewsById'
      ])
    },
    inject: ['sandbox'],
    methods: {
      loadedPreview () {
        if (this.$refs.frame && this.$refs.frame.srcdoc) {
          this.$emit('loaded', this.$refs.frame)
          this.resize()
        }
      },
      resize () {
        if (!this.$refs.frame) return
        const frameBody = this.$refs.frame.contentWindow.document.body

        // no scollbars
        frameBody.style.overflow = 'hidden'

        // get body extra margin
        const bodyStyle = window.getComputedStyle(frameBody)
        const bodyMarginTop = bodyStyle.getPropertyValue('margin-top')
        const bodyMarginBottom = bodyStyle.getPropertyValue('margin-bottom')
        const frameHeight = frameBody.scrollHeight + parseInt(bodyMarginTop) + parseInt(bodyMarginBottom)

        window.requestAnimationFrame(() => {
          this.$refs.frame.height = frameHeight + 'px'
        })
      }
    },
    mounted () {
      window.addEventListener('resize', this.resize)
    },
    beforeDestroy () {
      window.removeEventListener('resize', this.resize)
    }
  }
</script>

<style lang="scss" scoped>

  .editorIframe {
    cursor: pointer;
    overflow-y: hidden;
    padding: 5px;

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
    text-align: center;
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: center;
    color: rgba($color__text, 0.5);
    background-color: rgba($color_editor--active, 0.05);
    border: 1px solid rgba($color_editor--active, 0.33);
  }

  .editor__preview--dark .editorIframe__empty {
    color: rgba($color__background, 0.75);
    background-color: rgba($color_editor--active, 0.2);
    border: 1px solid rgba($color_editor--active, 0.5);
  }
</style>
