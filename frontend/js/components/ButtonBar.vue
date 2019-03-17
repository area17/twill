<template>
  <div :class="barClasses">
    <slot></slot>
  </div>
</template>

<script>
  export default {
    name: 'A17Buttonbar',
    props: {
      type: {
        type: String,
        default: 'button'
      },
      variant: {
        type: String,
        default: '' // visible
      }
    },
    computed: {
      barClasses: function () {
        return [
          `buttonbar`,
          this.variant ? `buttonbar--${this.variant}` : ''
        ]
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  $toolbar__height: 40px;
  $border__radius: 2px;

  .buttonbar {
    font-size: 0;
    border: 1px solid $color__fborder;
    clear: both;
    overflow: hidden;
    border-radius: 2px;
    display: flex;

    > a,
    > button {
      @include btn-reset;
      height: $toolbar__height - 2px;
      line-height: $toolbar__height - 2px;
      float: left;
      padding: 0 20px;
      position: relative;
      color: $color__f--text;
      background: $color__background;
      font-size: 15px;
      text-decoration: none;
      border-right: 1px solid $color__border--light;
      display: flex;
      flex-wrap: nowrap;
      align-items: center;
      justify-content: center;
      border-radius: 0;

      .icon {
        color: $color__icons;
        display: block;
      }

      &:not(.button--disabled):hover {
        color: $color__text;
        background: $color__f--bg;

        .icon {
          color: $color__text;
        }
      }

      &.button--disabled {
        opacity: 0.5;
        cursor: default;
      }
    }

    > a:first-child,
    > button:first-child {
      border-top-left-radius: $border__radius;
      border-bottom-left-radius: $border__radius;
    }

    > a:last-child,
    > button:last-child {
      border-top-right-radius: $border__radius;
      border-bottom-right-radius: $border__radius;
      border-right: 0 none;
    }

    &:hover {
      border-color: $color__border--focus;
    }
  }

  .buttonbar--visible {
    overflow: visible;
  }
</style>

<style lang="scss">
  @import '~styles/setup/_mixins-colors-vars.scss';
  $toolbar__height: 40px;
  $border__radius: 2px;

  .buttonbar {
    > .dropdown > button {
      @include btn-reset;
      height: $toolbar__height - 2px;
      line-height: $toolbar__height - 2px;
      float: left;
      padding: 0 20px;
      position: relative;
      color: $color__f--text;
      background: $color__background;
      font-size: 15px;
      text-decoration: none;
      border-right: 1px solid $color__border--light;
      display: flex;
      flex-wrap: nowrap;
      align-items: center;
      justify-content: center;
      border-radius: 0;

      .icon {
        color: $color__icons;
        display: block;
      }

      &:not(.button--disabled):hover {
        color: $color__text;
        background: $color__f--bg;

        .icon {
          color: $color__text;
        }
      }

      &.button--disabled {
        opacity: 0.5;
        cursor: default;
      }
    }

    > .dropdown:first-child > button {
      border-top-left-radius: $border__radius;
      border-bottom-left-radius: $border__radius;
    }

    > .dropdown:last-child > button {
      border-top-right-radius: $border__radius;
      border-bottom-right-radius: $border__radius;
      border-right: 0 none;
    }
  }
</style>
