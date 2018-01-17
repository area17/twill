<template>
  <div class="overlay" :class="overlayClasses" @mousedown="hide" @touchstart="hide">
    <div class="overlay__window" @mousedown.stop @touchstart.stop>
      <header class="overlay__header" v-if="overlayTitle">
        {{ overlayTitle }}
        <button class="overlay__close" type="button" @click="hide"><span v-svg symbol="close_modal"></span><span class="overlay__closeLabel">Close</span></button>
      </header>
      <div class="overlay__content" v-if="active" v-show="!hidden">
        <slot></slot><!--  v-if="active" -->
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  let htmlClass = 's--overlay'

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
      mode: {
        type: String,
        default: ''
      }
    },
    data: function () {
      return {
        active: false,
        hidden: true
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

        const html = document.documentElement

        this.active = true
        this.hidden = false

        html.classList.add(htmlClass)

        window.addEventListener('keyup', this.keyPressed)

        this.$emit('open')
      },
      mask: function () {
        const html = document.documentElement
        html.classList.remove(htmlClass)
      },
      hide: function () {
        if (!this.active) return

        this.hidden = true
        this.mask()
        this.$emit('close')
      },
      close: function (onClose) {
        if (!this.active) return

        this.active = false
        this.mask()

        window.removeEventListener('keyup', this.keyPressed)
        this.$emit('close')
      },
      keyPressed: function (event) {
        if (event.which === 27 || event.keyCode === 27) {
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
    height:60px;
    line-height:60px;
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
    height:16px + 19px + 19px;
    color:$color__text--light;
    padding:19px 20px;
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
