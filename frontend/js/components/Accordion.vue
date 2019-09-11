<template>
  <div class="accordion" :class="visibilityClasses">
    <button type="button" class="accordion__trigger" @click="onClickVisibility" :aria-expanded="visible ?  'true' : 'false'" >
      <slot name="accordion__title"></slot>
      <span class="accordion__value"><slot name="accordion__value"></slot></span>
      <span v-svg symbol="dropdown_module"></span>
    </button>
    <transition :css="false" :duration="275" @before-enter="beforeEnter" @before-leave="beforeLeave" @enter="enter" @leave="leave">
      <div class="accordion__dropdown" v-show="visible" :aria-hidden="!visible">
        <div class="accordion__list">
          <slot></slot>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  import VisibilityMixin from '@/mixins/toggleVisibility'

  export default {
    name: 'A17Accordion',
    mixins: [VisibilityMixin],
    watch: {
      open: function () {
        if (this.visible !== this.open) {
          this.visible = this.open
        }
      }
    },
    methods: {
      getMaxHeight: function () { // retrieve max height depending on the content height
        return Math.min(250, this.$el.querySelector('.accordion__list').clientHeight + 1)
      },
      beforeEnter: function (el) {
        el.style.maxHeight = '0px'
      },
      enter: function (el, done) {
        el.style.maxHeight = this.getMaxHeight() + 'px'
      },
      beforeLeave: function (el, done) {
        el.style.maxHeight = this.getMaxHeight() + 'px'
      },
      leave: function (el, done) {
        el.style.maxHeight = '0px'
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .accordion {
    border-bottom:1px solid $color__border--light;
    background-color:$color__background;
    transition: background-color .25s linear;
    overflow:hidden;
  }

  .accordion__trigger {
    @include accordion-trigger;
    color:$color__text;
    display:flex;
    flex-flow: row nowrap;
    align-items: center;

    &:hover,
    &:focus {
      background:$color__ultralight;
    }

    .icon {
      color:$color__text--light;
    }
  }

  .accordion__value {
    flex-grow:1;
    text-align:right;
    color:$color__text--light;
    padding-left:10px;
    overflow:hidden;

    > * {
      overflow:hidden;
      text-overflow: ellipsis;
    }
  }

  .accordion__dropdown {
    overflow:hidden;
    max-height:0;
    height:auto;
    transition: max-height 0.275s ease;
  }

  .accordion__list {
    border-top:1px solid $color__border--light;
    padding:12px 20px;
  }

  .accordion__fields {
    border-top:1px solid $color__border--light;
    padding:20px;
  }

  .accordion__list .accordion__fields {
    border-top:0 none;
    padding:8px 0;
  }

  /* Opened accordion */
  .s--open {
    background-color:$color__ultralight;

    .accordion__dropdown {
      max-height:250px;
      overflow-y: auto;
    }

    .icon {
      transform:rotate(180deg);
    }
  }
</style>

<style lang="scss">
  .accordion {
    .accordion__list {
      .input {
        margin-top: 0;

        + .input {
          margin-top: 10px;
        }
      }
    }
  }
</style>
