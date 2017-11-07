<template>
  <div class="dropdown" :aria-title="title" :class="dropdownClasses">
    <slot></slot>
    <transition name="fade_move_dropdown">
      <div class="dropdown__position" v-if="active">
        <div class="dropdown__content" :style="offsetStyle" data-dropdown-content>
          <div class="dropdown__inner">
            <span class="dropdown__arrow" v-if="arrow"></span>
            <span class="dropdown__title f--small" v-if="title">{{ title }}</span>
            <slot name="dropdown__content">
            </slot>
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
      }
    },
    data: function () {
      return {
        currentPosition: this.position,
        currentHeight: 100,
        active: false
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
          'dropdown--full': this.width === 'full'
        }
      },
      offsetStyle: function () {
        return {
          'margin-top': this.isPosition('bottom') ? this.offset + 'px' : '',
          'margin-bottom': this.isPosition('top') ? this.offset + 'px' : '',
          'max-width': this.maxWidth > 0 && this.width !== 'full' ? this.maxWidth + 'px' : ''
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
      setHeight: function () {
        // save current height of the dropdown for positioning purpose
        this.currentHeight = this.$el.querySelector('[data-dropdown-content]') ? this.$el.querySelector('[data-dropdown-content]').offsetHeight : 100
      },
      closeFromDoc: function (event) {
        var self = this

        const target = event.target

        if (!this.clickable) self.close()
        else if (!self.$el.querySelector('[data-dropdown-content]').contains(target) && this.clickable) self.close()
      },
      open: function (onShow) {
        var self = this

        if (this.active) return

        document.body.click() // close other dropdown

        // timeout so the click is not triggered directly
        this.timer = setTimeout(function () {
          self.timer = null
          self.active = true

          document.addEventListener('click', self.closeFromDoc, true)
          document.addEventListener('touchstart', self.closeFromDoc, true)

          self.$nextTick(function () {
            self.setHeight()
            self.reposition()
          })

          self.$emit('open')
        }, 1)
      },
      close: function (onClose) {
        var self = this

        if (!this.active) return

        clearTimeout(this.timer)
        document.removeEventListener('click', this.closeFromDoc, true)
        document.removeEventListener('touchstart', this.closeFromDoc, true)

        setTimeout(function () {
          self.active = false
          self.$emit('close')
        }, 1)
      },
      toggle: function (onToggle) {
        if (this.active) this.close()
        else this.open()
      }
    },
    beforeDestroy: function () {
      // if (this.$el.parentNode) {
      //   this.$el.parentNode.removeChild(this.$el)
      // }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../scss/setup/variables.scss";
  @import "../../scss/setup/colors.scss";
  @import "../../scss/setup/mixins.scss";

  .dropdown {
    display:inherit;
    position:relative;
  }

  .dropdown__position {
    position: absolute;
    z-index:$zindex__dropdown;
  }

  .dropdown__content {

  }

  // .dropdown--active {
  //   .dropdown__content {
  //   }
  // }

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

    /deep/ button {
      @include btn-reset;
      width:100%;
      background:transparent;
      text-align:left;
      width:100%;
    }

    /deep/ a,
    /deep/ button {
      display:block;
      color:$color__text--light;
      font-size:1em;
      padding:0 15px;
      padding-right:50px;
      height:40px;
      line-height: 40px;
      text-decoration: none;
      white-space: nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      border-radius:0;

      &:hover {
        color:$color__text;
        background:$color__light;
      }

      &.dropdown__active {
        color:$color__text;
        background:$color__light;
      }

      .icon {
        margin-right:10px;
      }
    }

    /deep/ .checkboxGroup__item,
    /deep/ .radioGroup__item {
      margin:0 -15px;
      padding-right:50px;
      padding-left:15px;
      display:block;
    }

    /deep/ .checkbox,
    /deep/ .checkbox label {
      display:block;
    }
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
    background:rgba($color__background,0.95);
    border-radius:2px;
    box-shadow:0 0px 8px rgba(0,0,0,0.3);
    padding:10px 0;

    /deep/ .input {
      margin-top:0;
      padding:0 15px;
    }
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
      box-shadow:0 0px 8px rgba(0,0,0,0.3);
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

  .dropdown--center .dropdown__arrow {
    left: 50%;
    margin-left:-25px;
  }


</style>
