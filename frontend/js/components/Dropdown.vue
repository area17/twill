<template>
  <div class="dropdown" :aria-title="title" :class="dropdownClasses">
    <div v-if="fixed" ref="dropdown__cta">
      <slot></slot>
    </div>
    <slot v-else></slot>
    <transition name="fade_move_dropdown">
      <div class="dropdown__position" ref="dropdown__position" v-if="active">
        <div class="dropdown__content" :style="offsetStyle" data-dropdown-content>
          <div class="dropdown__inner">
            <span class="dropdown__arrow" v-if="arrow"></span>
            <div class="dropdown__scroller" :style="innerStyle">
              <span class="dropdown__title f--small" v-if="title">{{ title }}</span>
              <slot name="dropdown__content">
              </slot>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  export default {
    name: 'A17Dropdown',
    props: {
      title: {
        type: String,
        default: ''
      },
      position: {
        type: String,
        default: 'bottom' // bottom, top, bottom-right, bottom-left, bottom-center, top-left, top-right, top-center
      },
      width: {
        type: String,
        default: 'auto' // auto, full
      },
      maxWidth: {
        type: Number,
        default: 300
      },
      maxHeight: {
        type: Number,
        default: 0
      },
      minWidth: {
        type: Number,
        default: 0
      },
      arrow: {
        type: Boolean,
        default: false
      },
      clickable: { // content inside should be clickable (without closing the dropdown, useful for checkboxes for example)
        type: Boolean,
        default: false
      },
      offset: {
        type: Number,
        default: 5
      },
      sideOffset: {
        type: Number,
        default: 0
      },
      fixed: {
        type: Boolean,
        default: false
      }
    },
    data: function () {
      return {
        currentPosition: this.position,
        currentHeight: 100,
        currentMaxWidth: this.maxWidth,
        active: false,
        originScrollPostion: null,
        scrollOffset: 75
      }
    },
    computed: {
      dropdownClasses: function () {
        return {
          'dropdown--active': this.active,
          'dropdown--arrow': this.arrow,
          'dropdown--bottom': this.isPosition('bottom'),
          'dropdown--top': this.isPosition('top'),
          'dropdown--left': this.isPosition('left'),
          'dropdown--right': this.isPosition('right'),
          'dropdown--center': this.isPosition('center'),
          'dropdown--full': this.width === 'full',
          'dropdown--fixed': this.fixed
        }
      },
      offsetStyle: function () {
        return {
          'margin-top': this.isPosition('bottom') ? this.offset + 'px' : '',
          'margin-bottom': this.isPosition('top') ? this.offset + 'px' : '',
          transform: this.sideOffset ? 'translateX(' + this.sideOffset + 'px)' : '',
          'max-width': this.currentMaxWidth > 0 && this.width !== 'full' ? this.currentMaxWidth + 'px' : '',
          'min-width': this.minWidth > 0 ? this.minWidth + 'px' : ''
        }
      },
      innerStyle: function () {
        return {
          'max-height': this.maxHeight > 0 ? this.maxHeight + 'px' : '',
          overflow: this.maxHeight > 0 ? 'hidden' : '',
          'overflow-y': this.maxHeight > 0 ? 'auto' : ''
        }
      }
    },
    methods: {
      isPosition: function (type) {
        return this.currentPosition.indexOf(type) !== -1
      },
      reposition: function () {
        const yLimitBottom = this.$el.getBoundingClientRect().top + this.$el.offsetHeight + window.pageYOffset + this.offset
        const yLimitTop = this.$el.getBoundingClientRect().top + window.pageYOffset - this.offset
        const yWin = window.pageYOffset + window.innerHeight

        // revert to original desired position
        if (this.currentPosition !== this.position) this.currentPosition = this.position

        if (this.isPosition('bottom')) {
          if ((yLimitBottom + this.currentHeight) > yWin) this.currentPosition = this.currentPosition.replace(/bottom/i, 'top') // reposition from bottom to top
        } else if (this.isPosition('top')) {
          if ((yLimitTop - this.currentHeight) < window.pageYOffset) this.currentPosition = this.currentPosition.replace(/top/i, 'bottom') // reposition from top to bottom
        }
      },
      getHeight: function () {
        // save current height of the dropdown for positioning purpose
        this.currentHeight = this.$el.querySelector('[data-dropdown-content]') ? this.$el.querySelector('[data-dropdown-content]').offsetHeight : 100
      },
      setMaxWidth: function () {
        // adjust max width if larger than the viewport
        const elPosition = this.$el.getBoundingClientRect()

        if (this.isPosition('left')) this.currentMaxWidth = (this.maxWidth + elPosition.left) > window.innerWidth ? window.innerWidth - elPosition.left : this.maxWidth
        else if (this.isPosition('right')) this.currentMaxWidth = (this.maxWidth + (window.innerWidth - elPosition.right)) > window.innerWidth ? window.innerWidth - (window.innerWidth - elPosition.right) : this.maxWidth
        else this.currentMaxWidth = this.maxWidth > window.innerWidth ? window.innerWidth : this.maxWidth
      },
      setFixedPosition: function () {
        const ctaPosition = this.$refs.dropdown__cta.getBoundingClientRect()

        // Top / Bottom position
        if (this.isPosition('top')) {
          this.$refs.dropdown__position.style.bottom = Math.round(window.innerHeight - ctaPosition.bottom + ctaPosition.height) + 'px'
        } else {
          this.$refs.dropdown__position.style.top = Math.round(ctaPosition.top + ctaPosition.height) + 'px'
        }

        // Left / Right / Center position
        if (this.isPosition('left')) {
          this.$refs.dropdown__position.style.left = Math.round(ctaPosition.left) + 'px'
        } else if (this.isPosition('right')) {
          this.$refs.dropdown__position.style.right = Math.round(window.innerWidth - ctaPosition.right) + 'px'
        } else {
          this.$refs.dropdown__position.style.left = Math.round(ctaPosition.left + ctaPosition.width / 2) + 'px'
        }
      },
      closeFromDoc: function (event) {
        const target = event.target

        if (event.type === 'scroll') {
          if (this.$el.querySelector('[data-dropdown-content]').contains(target)) return
          const scrollPos = window.pageYOffset || document.documentElement.scrollTop
          if (scrollPos > this.originScrollPostion - this.scrollOffset && scrollPos < this.originScrollPostion + this.scrollOffset) {
            this.setFixedPosition()
            return
          }
        }

        if (!this.clickable) this.close()
        else if (!this.$el.querySelector('[data-dropdown-content]').contains(target) && this.clickable) this.close()
      },
      open: function (onShow) {
        if (this.active) return

        document.body.click() // close other dropdown

        // timeout so the click is not triggered directly
        this.timer = setTimeout(() => {
          this.timer = null
          this.active = true

          document.addEventListener('click', this.closeFromDoc, true)
          document.addEventListener('touchend', this.closeFromDoc, true)

          if (this.fixed) {
            window.addEventListener('scroll', this.closeFromDoc, true)
            this.originScrollPostion = window.pageYOffset || document.documentElement.scrollTop
          }

          this.$nextTick(function () {
            this.getHeight()
            this.reposition()
            this.setMaxWidth()
            this.fixed && this.setFixedPosition()
          })

          this.$emit('open')
        }, 1)
      },
      close: function (onClose) {
        if (!this.active) return

        clearTimeout(this.timer)
        document.removeEventListener('click', this.closeFromDoc, true)
        document.removeEventListener('touchend', this.closeFromDoc, true)

        if (this.fixed) {
          window.removeEventListener('scroll', this.closeFromDoc, true)
          this.originScrollPostion = null
          this.active = false
          this.$emit('close')
          return
        }

        setTimeout(() => {
          this.active = false
          this.$emit('close')
        }, 0)
      },
      toggle: function (onToggle) {
        if (this.active) this.close()
        else this.open()
      }
    }
  }
</script>

<style lang="scss" scoped>

  .dropdown {
    display:inherit;
    position:relative;
  }

  .dropdown__position {
    position: absolute;
    z-index:$zindex__dropdown;

    .dropdown--fixed & {
      position: fixed;
    }
  }

  .dropdown--bottom .dropdown__position {
    top: 100%;
  }

  .dropdown--top .dropdown__position {
    bottom: 100%;
  }

  .dropdown--left .dropdown__position {
    left:0;
  }

  .dropdown--center .dropdown__position {
    left:50%;

    .dropdown__content {
      transform:translateX(-50%);
    }
  }

  .dropdown--right .dropdown__position {
    right:0;
  }

  .dropdown__title {
    height:35px;
    line-height: 35px;
    white-space: nowrap;
    overflow:hidden;
    padding:0 15px;
    border-bottom:1px solid $color__border--light;
    display:block;
    margin-bottom:10px;
    color:$color__text--light;
  }

  .dropdown__content {
    max-width:300px;
  }

  .dropdown--full .dropdown__position {
    max-width:100%;
    width:100%;

    .dropdown__content {
      max-width:100%;
      width:100%;
    }
  }

  .dropdown__inner {
    position:relative;
    background:rgba($color__background,0.98);
    border-radius:2px;
    box-shadow:$box-shadow;
    max-width:calc(100vw - 10px);
  }

  .dropdown__scroller {
    padding:10px 0;
  }

  .dropdown--arrow.dropdown--bottom .dropdown__content {
    margin-top:15px;
  }

  .dropdown--arrow.dropdown--top .dropdown__content {
    margin-bottom:15px;
  }

  .dropdown--arrow .dropdown__arrow {
    left: 50%;
    pointer-events: none;
    width:10px + 20px + 20px;
    height:10px;
    overflow:hidden;
    position: absolute;
    // border-bottom:1px solid $color__background;

    &::after {
      border: solid transparent;
      content: "";
      left:50%;
      display:block;
      margin-top:5px;
      margin-left:-5px;
      position: absolute;
      width:10px;
      height:10px;
      background-color:$color__background;
      box-shadow:$box-shadow;
      transform: rotate(45deg);
    }
  }

  .dropdown--bottom .dropdown__arrow {
    bottom: 100%;
  }

  .dropdown--top .dropdown__arrow {
    bottom: -10px;
    transform: rotate(180deg);

    &::after {
      transform: rotate(45deg);
    }
  }

  .dropdown--left .dropdown__arrow {
    left:0;
  }

  .dropdown--right .dropdown__arrow {
    right:0;
    left:auto;
  }

  .dropdown--center .dropdown__arrow {
    left: 50%;
    margin-left:-25px;
  }
</style>

<style lang="scss">

  .dropdown {
    .dropdown__content {
      button {
        @include btn-reset;
        width: 100%;
        background: transparent;
        text-align: left;

        &:disabled {
          cursor: default;
          pointer-events: none;
          opacity: .5;
        }
      }

       a,
       button {
        display: block;
        color: $color__text--light;
        font-size: 1em;
        padding: 0 15px;
        padding-right: 50px;
        height: 40px;
        line-height: 40px;
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-radius: 0;

        &:hover {
          color: $color__text;
          background: $color__light;
        }

        &.dropdown__active {
          color: $color__text;
          background: $color__light;
        }

        .icon {
          margin-right: 10px;
        }
      }

       .checkboxGroup__item,
       .radioGroup__item {
        margin: 0 -15px;
        padding-right: 50px;
        padding-left: 15px;
        display: block;
      }

       .checkbox,
       .checkbox label {
        display: block;
      }
    }

    .dropdown__inner {
      .input {
        margin-top: 0;
        padding: 0 15px;
      }
    }
  }
</style>
