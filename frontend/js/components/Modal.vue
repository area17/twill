<template>
  <div class="modal" :class="modalClasses" @mousedown="close" @touchstart="close">
    <transition name="fade_scale_modal">
      <div class="modal__window" @mousedown.stop @touchstart.stop v-if="active">
        <header class="modal__header" v-if="modalTitle">
          {{ modalTitle }}
          <button class="modal__close" type="button" @click="close"><span v-svg symbol="close_modal"></span></button>
        </header>

        <div class="modal__content">
          <slot></slot>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  let htmlClass = 's--modal'

  export default {
    name: 'A17Modal',
    props: {
      title: {
        type: String,
        default: ''
      },
      mode: {
        type: String,
        default: ''
      }
    },
    data: function () {
      return {
        active: false
      }
    },
    computed: {
      modalTitle: function () {
        return this.title !== '' ? this.title : this.browserTitle
      },
      modalClasses: function () {
        return {
          'modal--active': this.active,
          'modal--tiny': this.mode === 'tiny',
          'modal--medium': this.mode === 'medium',
          'modal--wide': this.mode === 'wide'
        }
      },
      ...mapState({
        browserTitle: state => state.browser.title
      })
    },
    methods: {
      open: function (onShow) {
        if (this.active) return

        const html = document.documentElement

        this.active = true

        html.classList.add(htmlClass)

        window.addEventListener('keyup', this.keyPressed)

        // auto focus first field
        this.$nextTick(function () {
          const field = this.$el.querySelector('input, textarea, select')
          if (field) field.focus()
        })

        this.$emit('open')
      },
      close: function (onClose) {
        if (!this.active) return

        const html = document.documentElement

        this.active = false

        html.classList.remove(htmlClass)

        window.removeEventListener('keyup', this.keyPressed)

        this.$emit('close')
      },
      keyPressed: function (onKey) {
        if (event.which === 27 || event.keyCode === 27) {
          this.close()
          this.$emit('esc-key')
        }
      }
    },
    beforeDestroy: function () {
      if (this.$el.parentNode) {
        if (this.active) window.removeEventListener('keyup', this.keyPressed)
        this.$el.parentNode.removeChild(this.$el)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../scss/setup/variables.scss";
  @import "../../scss/setup/colors.scss";
  @import "../../scss/setup/mixins.scss";

  .modal {
    position:fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(0,0,0,.66);
    z-index: $zindex__modal;

    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;

    opacity: 0;
    visibility: hidden;
    transition: opacity 0.35s ease, visibility 0s 0.35s;

    &.modal--wide {
      background-color: $color__modal--wide;
    }
  }

  .modal__window {
    background:$color__background;
    width: calc(100vw - 40px);
    max-width:650px;
    position: relative;
    border-radius:2px;
    display:flex;
    flex-flow: column nowrap;
  }

  .modal__content {
    overflow:hidden;
    overflow-y: auto;
    flex-grow: 1;
    height:100%;
  }

  .modal__header {
    border-top-left-radius:2px;
    border-top-right-radius:2px;
    background: $color__modal--header;
    padding:0 20px;
    height:50px;
    line-height:50px;
    position:relative;
    font-weight:bold;
  }

  .modal__close {
    @include btn-reset;
    position:absolute;
    right:5px;
    top:2px;
    background:transparent;
    height:16px + 30px;
    width:16px + 30px;
    color:$color__icons;
    padding:15px;

    &:hover {
      color:$color__text;
    }
  }

  .modal__content {
    padding:0 20px;

    > button {
      margin-bottom:20px;
    }
  }

  /* Modal Tiny Size */
  .modal--tiny {

    .modal__window {
      width: calc(100vw - 40px);
      max-width: 350px;
      margin-bottom:25vh;
    }

    .modal__header {
      display:none;
    }
  }

  /* Modal Medium Size */
  .modal--medium {

    .modal__window {
      width: calc(100vw - 40px);
      max-width: 830px;
      // max-width: inherit;
      min-height: 66vh;
      max-height:100%;
    }

    .modal__content {
      // padding:0;
      display: flex;
    }
  }

  /* Modal Wide Size */
  .modal--wide {
    padding: 30px;

    .modal__content {
      padding:0;
      display: flex;
    }

    .modal__window {
      width: 100%;
      height: 100%;
      max-width: inherit;
    }
  }

  /* Modal with form */
  .modal--form {
    .modal__content {
      padding-bottom:20px;
    }
  }

  /* Modal with strating Intro */
  .modal--withintro {
    .modal__content {
      padding-top:20px;
    }
  }

  /* Modal used for the browser */
  .modal--browser {
    .modal__content {
      padding-left:0;
      padding-right:0;
    }
  }

  /* Modal used with cropper */
  .modal--cropper .modal__content {
    position: relative;
  }

  /* Active Modal */
  .modal--active { // centered into the page
    opacity: 1;
    visibility: visible;
    transition: opacity 0.35s;
  }

  /* Tiny modal option */
 .modal--tiny /deep/ .modal--tiny-title {
   margin-bottom: 20px;
 }
</style>
