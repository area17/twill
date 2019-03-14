<template>
  <div class="modal" :class="modalClasses" @mousedown="hide" @touchend.prevent="hide">
    <transition name="fade_scale_modal">
      <div class="modal__window" @mousedown.stop @touchend.stop v-if="active" v-show="!hidden">
        <header class="modal__header" v-if="modalTitle">
          {{ modalTitle }}
          <button class="modal__close" type="button" @click="hide"><span v-svg symbol="close_modal"></span></button>
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
  import htmlClasses from '@/utils/htmlClasses'

  const html = document.documentElement
  const htmlClass = htmlClasses.modal

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
      },
      forceClose: {
        type: Boolean,
        default: false
      },
      forceLock: {
        type: Boolean,
        default: false
      }
    },
    data: function () {
      return {
        active: false,
        hidden: true,
        locked: false,
        firstFocusableEl: null,
        lastFocusableEl: null
      }
    },
    computed: {
      modalTitle: function () {
        return this.title !== '' ? this.title : this.browserTitle
      },
      modalClasses: function () {
        return {
          'modal--active': this.active,
          'modal--hidden': this.hidden,
          'modal--tiny': this.mode === 'tiny',
          'modal--medium': this.mode === 'medium',
          'modal--wide': this.mode === 'wide'
        }
      },
      ...mapState({
        browserTitle: state => state.browser.title
      })
    },
    watch: {
      forceLock: function () {
        this.locked = this.forceLock
      }
    },
    methods: {
      open: function (focusable = true) {
        if (this.active && !this.hidden) {
          return
        }

        this.active = true
        this.hidden = false

        html.classList.add(htmlClass)

        this.bindKeyboard()

        // auto focus first field
        this.$nextTick(function () {
          if (focusable) {
            const focusableSelector = 'textarea, input:not([type="hidden"]), select, button[type="submit"]'
            const focusableNodes = this.$el.querySelectorAll(focusableSelector)
            const allFocusableNodes = this.$el.querySelectorAll(focusableSelector + ', a, button[type="button"]')

            // Trap focus inside the modal
            this.firstFocusableEl = this.$el.querySelector('.modal__close')
            this.lastFocusableEl = allFocusableNodes[allFocusableNodes.length - 1]

            // init focus
            if (focusableNodes.length) focusableNodes[0].focus()
          }
          this.$emit('open')
        })
      },
      mask: function () {
        html.classList.remove(htmlClass)
        this.unbindKeyboard()
        this.$emit('close')
      },
      hide: function () {
        if (!this.active) return
        if (this.locked) return

        if (this.forceClose) {
          this.close()
          return
        }

        this.hidden = true
        this.mask()
      },
      close: function (onClose) {
        if (!this.active) return
        if (this.locked) return

        this.active = false
        this.mask()
      },
      bindKeyboard: function () {
        window.addEventListener('keyup', this.keyPressed)
        document.addEventListener('keydown', this.keyDown, false)
      },
      unbindKeyboard: function () {
        window.removeEventListener('keyup', this.keyPressed)
        document.removeEventListener('keydown', this.keyDown)
      },
      keyPressed: function (event) {
        if (event.which === 27 || event.keyCode === 27) {
          this.hide()
          this.$emit('esc-key')
        }
      },
      keyDown: function (event) {
        // tab
        if (event.keyCode && event.keyCode === 9) {
          if (event.shiftKey) {
            // backwards
            if (document.activeElement.isEqualNode(this.firstFocusableEl)) {
              this.lastFocusableEl.focus()
              event.preventDefault()
            }
          } else {
            // onwards
            if (document.activeElement.isEqualNode(this.lastFocusableEl)) {
              this.firstFocusableEl.focus()
              event.preventDefault()
            }
          }
        }
      }
    },
    beforeDestroy: function () {
      if (this.$el.parentNode) {
        if (this.active) this.unbindKeyboard()
        this.$el.parentNode.removeChild(this.$el)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .modal {
    position:fixed;
    top: 0;
    right: 0;
    height: 0;
    left: 0;
    background: rgba(0,0,0,.66);
    z-index: $zindex__modal;

    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    overflow-x: hidden;
    overflow-y: auto;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.35s ease, visibility 0s 0.35s, height 0s 0.35s;

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
    margin:auto;
  }

  .modal__content {
    overflow:hidden;
    overflow-y: auto;
    flex-grow: 1;
    max-height:100%;
  }

  .modal__header {
    border-top-left-radius:2px;
    border-top-right-radius:2px;
    background: $color__modal--header;
    padding:0 20px;
    height:50px;
    line-height:50px;
    position:relative;
    font-weight:600;
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

    &:hover,
    &:focus {
      color:$color__text;
    }
  }

  .modal__content {
    padding:0 20px;

    > button {
      margin-bottom:20px;
    }
  }

  /* Modal Wide Size */
  .modal--wide {
    padding: 30px;

    .modal__content {
      padding: 0;
      display: flex;
    }

    .modal__window {
      width: 100%;
      height: 100%;
      max-width: inherit;

      @include breakpoint(xsmall) {
        border-radius: 0;
      }

    }

    @include breakpoint(small) {
      padding: 10px;
    }

    @include breakpoint(xsmall) {
      width: 100%;
      min-height: 100%;
      padding: 0px;
    }
  }

  /* Modal Medium Size */
  .modal--medium {

    .modal__window {
      width: calc(100vw - 40px);
      max-width: 830px;
      // max-width: inherit;
      min-height: 66vh;
      max-height: 100%;
    }

    .modal__content {
      // padding:0;
      display: flex;
    }
  }

  /* Modal Tiny Size */
  .modal--tiny {

    .modal__window {
      width: calc(100vw - 40px);
      max-width: 350px;
      height: auto;
      margin-bottom:40vh;
    }

    .modal__content {
      padding: 0 20px;
      display: block;
    }

    .modal__header {
      display:none;
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
    height:100%;
    transition: opacity 0.35s;
  }

  .modal--hidden {
    display: none;
  }
</style>

<style lang="scss">
  /* Tiny modal option */
  .modal .modal--tiny .modal--tiny-title {
    margin-bottom: 20px;
  }
</style>
