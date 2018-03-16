<template>
  <transition name="fade_spinner" @before-enter="beforeEnter" @after-enter="afterEnter" @before-leave="beforeLeave">
    <div class="a17spinner">
      <div class="a17spinner__anim" :class="{ 'a17spinner__anim--visible' : isVisible }">
        <span class="loader"><span></span></span>
        <!-- <slot></slot> -->
      </div>
    </div>
  </transition>
</template>

<script>
  export default {
    name: 'A17Spinner',
    props: {
      visible: {
        type: Boolean,
        default: false
      }
    },
    data: function () {
      return {
        isVisible: this.visible
      }
    },
    methods: {
      beforeEnter: function (el) {
        this.isVisible = this.visible
      },
      afterEnter: function (el) {
        this.isVisible = true
      },
      beforeLeave: function (el) {
        this.isVisible = false
      }
    }
  }
</script>

<style lang="scss"> // beware : not scoped
  @import '~styles/setup/_mixins-colors-vars.scss';

  .a17spinner {
    display: flex;
    width: 100%;
    // height: 50px;
    padding: 10vh 0;
    background-color: rgba($color__background, 0.75);
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: $zindex__loadingTable;
  }

  .a17spinner__anim {
    margin: 100px auto;
    width: 20px;
    height: 20px;
    position: relative;
    text-align: center;
    color:$color__text--light;
    opacity:0;
    transition: opacity .25s linear;
    transition-delay:0.5s;

    &.a17spinner__anim--visible {
      opacity:1;
    }
  }

  .app--form .a17spinner {
    background-color: rgba($color__border--light, 0.75);
  }

  .s--in-editor .overlay .a17spinner {
    background-color: $color__background;
    .a17spinner__anim {
      transition-delay:0s;
    }
  }
</style>
