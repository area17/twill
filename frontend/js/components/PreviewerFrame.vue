<template>
  <iframe :srcdoc="content" frameborder="0" class="previewerframe" :style="{ width: size > 0 ? size + 'px' : '' }" @load="loadPreview"></iframe>
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
        let self = this

        // disable button and link in preview
        const iframe = event.target
        const buttons = iframe.contentDocument.querySelectorAll('a,button')

        for (let i = 0; i < buttons.length; i++) {
          buttons[i].setAttribute('disabled', 'disabled')
          buttons[i].style.pointerEvents = 'none'
          buttons[i].onclick = function () {
            return false
          }
        }

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
  @import '~styles/setup/_mixins-colors-vars.scss';

  .previewerframe {
    width: 100%;
    height:100%;
    margin: 0 auto;
    max-width:calc(100% - 20px);
    display: block;
    // box-shadow:0 0px 10px rgba(0,0,0,0.8);
    transform:translateX(-50%);
    transition: width .3s ease;
    position: absolute;
    top:0;
    bottom:0;
    left:50%;
    background:$color__background;
  }
</style>
