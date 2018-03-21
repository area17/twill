<template>
  <div class="overlay" :class="overlayClasses">
    <div class="overlay__window">
      <header class="overlay__header" v-if="overlayTitle">
        {{ overlayTitle }}
        <button class="overlay__close" type="button" @click="hide"><span v-svg symbol="close_modal"></span><span class="overlay__closeLabel">Close</span></button>
      </header>
      <div class="overlay__content" v-if="active" v-show="!hidden">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import htmlClasses from '@/utils/htmlClasses'

  const html = document.documentElement
  const htmlOverlayClass = htmlClasses.overlay
  const htmlModalClass = htmlClasses.modal

  export default {
    name: 'A17Overlay',
    props: {
      title: {
        type: String,
        default: ''
      },
      revisionTitle: {
        type: String,
        default: 'Revision history'
      },
      forceClose: {
        type: Boolean,
        default: false
      },
      forceLock: {
        type: Boolean,
        default: false
      },
      mode: {
        type: String,
        default: ''
      }
    },
    data: function () {
      return {
        active: false,
        hidden: true,
        locked: false
      }
    },
    computed: {
      activeRevision: function () {
        return Object.keys(this.currentRevision).length
      },
      overlayTitle: function () {
        return this.activeRevision ? this.revisionTitle : this.title
      },
      overlayClasses: function () {
        return {
          'overlay--active': this.active,
          'overlay--hidden': this.hidden
        }
      },
      ...mapState({
        currentRevision: state => state.revision.active
      })
    },
    methods: {
      open: function (onShow) {
        if (this.active && !this.hidden) {
          return
        }

        this.active = true
        this.hidden = false

        html.classList.add(htmlOverlayClass)

        window.addEventListener('keyup', this.keyPressed)
        this.$emit('open')
      },
      mask: function () {
        html.classList.remove(htmlOverlayClass)

        window.removeEventListener('keyup', this.keyPressed)
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
      keyPressed: function (event) {
        if (event.which === 27 || event.keyCode === 27) {
          // Lets not close the overlay if we already have a modal opened on top of the overlay
          if (html.classList.contains(htmlModalClass)) return
          this.hide()
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
  @import '~styles/setup/_mixins-colors-vars.scss';

  $height__header:60px;

  .overlay {
    position:fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(0,0,0,.4);
    z-index: $zindex__overlay;

    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;

    opacity: 0;
    visibility: hidden;
    transition: opacity 0.35s ease, visibility 0s 0.35s;

    padding: 0;
    background-color: $color__overlay--background;
  }

  .overlay + .overlay {
    z-index: $zindex__overlay + 1;
  }

  .overlay__window {
    background:$color__background;
    min-width: 50vw;
    position: relative;
    border-radius:2px;
    display:flex;
    flex-flow: column nowrap;
    width: 100%;
    height: 100%;
    max-width: inherit;
  }

  .overlay__content {
    overflow:hidden;
    flex-grow: 1;
    height:100%;
  }

  .overlay__header {
    background: $color__overlay--header;
    color:$color__background;
    padding:0 20px;
    height:$height__header;
    line-height:$height__header;
    position:relative;
    font-weight:600;
    text-align:center;
    @include font-smoothing();
  }

  .overlay__close {
    @include btn-reset;
    position:absolute;
    left:0;
    top:0;
    background:transparent;
    height:$height__header;
    color:$color__text--light;
    padding:#{($height__header - 16px) / 2 } 20px;
    text-align:left;

    &:hover,
    &:focus {
      color:$color__background;
    }
  }

  .overlay__closeLabel {
    position:relative;
    margin-left:10px;
    top:-2px;
  }

  .overlay__content {
    padding:0;
    display: flex;

    > button {
      margin-bottom:20px;
    }
  }

  .overlay--active { // centered into the page
    opacity: 1;
    visibility: visible;
    transition: opacity 0.35s;
  }

  .overlay--hidden {
    display: none;
  }

</style>
