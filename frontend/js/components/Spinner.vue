<template>
  <transition name="fade_spinner"  @after-enter="afterEnter" @before-leave="beforeLeave">
    <div class="spinner">
      <div class="spinner__anim" :class="{ 'spinner__anim--visible' : visible }">
        <svg width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
            <g fill="none" fill-rule="evenodd">
                <g transform="translate(1 1)" stroke-width="2">
                    <circle stroke-opacity=".5" cx="18" cy="18" r="18"/>
                    <path d="M36 18c0-9.94-8.06-18-18-18">
                        <animateTransform
                            attributeName="transform"
                            type="rotate"
                            from="0 18 18"
                            to="360 18 18"
                            dur="1s"
                            repeatCount="indefinite"/>
                    </path>
                </g>
            </g>
        </svg>
        <!-- <slot></slot> -->
      </div>
    </div>
  </transition>
</template>

<script>
  export default {
    name: 'A17Spinner',
    data: function () {
      return {
        visible: false
      }
    },
    methods: {
      afterEnter: function (el) {
        this.visible = true
      },
      beforeLeave: function (el) {
        this.visible = false
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .spinner {
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

  .spinner__anim {
    margin: 100px auto;
    width: 20px;
    height: 20px;
    position: relative;
    text-align: center;
    color:$color__text--light;
    opacity:0;
    transition: opacity .25s linear;
    transition-delay:0.5s;

    &.spinner__anim--visible {
      opacity:1;
    }
  }
</style>
