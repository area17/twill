<template>
  <iframe :srcdoc="content" frameborder="0" class="previewerframe" :style="{ width: size > 0 ? size + 'px' : '' }"
          @load="loadPreview"></iframe>
</template>

<script>
  export default {
    name: 'A17previewerFrame',
    props: {
      size: {
        type: Number,
        default: 0
      },
      content: {
        type: String,
        default: ''
      },
      scrollPosition: {
        type: Number,
        default: 0
      }
    },
    data: function () {
      return {
        currentScroll: this.scrollPosition
      }
    },
    watch: {
      scrollPosition: function (value) {
        // scroll the iframe
        this.$el.contentWindow.scrollTo(0, value)
      }
    },
    methods: {
      loadPreview: function (event) {
        const self = this

        // disable links in preview (but enable button)
        const iframe = event.target
        const links = Array.from(iframe.contentDocument.querySelectorAll('a:not(.sf-dump-toggle)') || [])

        links.forEach((link) => {
          // desactivate all link that are not anchor links
          if (!link.getAttribute('href').startsWith('#') || link.getAttribute('href') === '#') {
            link.setAttribute('disabled', 'disabled')
            link.style.pointerEvents = 'none'
          }
          // make sure links with anchors can have some JS enabled
          link.onclick = function (event) {
            if (!event.defaultPrevented) {
              return false
            }
          }
        })

        const forms = Array.from(iframe.contentDocument.querySelectorAll('form') || [])

        forms.forEach(form => {
          form.addEventListener('submit', (event) => {
            event.preventDefault()
          }, true)
        })

        iframe.contentDocument.addEventListener('scroll', function (event) {
          const scrollValue = iframe.contentWindow.pageYOffset

          if (scrollValue !== self.currentScroll) {
            self.$emit('scrollDoc', scrollValue)
            self.currentScroll = scrollValue
          }
        })

        // move the iframe after load
        this.$el.contentWindow.scrollTo(0, this.currentScroll)
      }
    }
  }
</script>

<style lang="scss" scoped>

  .previewerframe {
    width: 100%;
    height: 100%;
    margin: 0 auto;
    max-width: calc(100% - 20px);
    display: block;
    // box-shadow:0 0px 10px rgba(0,0,0,0.8);
    transform: translateX(-50%);
    transition: width .3s ease;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 50%;
    background: $color__background;
  }
</style>
