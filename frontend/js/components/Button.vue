<template>
  <button :type="type" :class="buttonClasses" :disabled="disabled" @click="onClick">
    <slot></slot>
  </button>
</template>

<script>
  export default {
    name: 'A17Button',
    props: {
      type: {
        type: String,
        default: 'button'
      },
      variant: {
        type: String,
        default: '' // validate, action, secondary, ghost, aslink, aslink-grey
      },
      icon: {
        default: ''
      },
      disabled: {
        type: Boolean,
        default: false
      },
      size: {
        type: String,
        default: '' // small
      }
    },
    computed: {
      buttonClasses: function () {
        return [
          `button`,
          this.size ? `button--${this.size}` : '',
          this.variant ? `button--${this.variant}` : '',
          this.icon ? `button--icon button--${this.icon}` : ''
        ]
      }
    },
    methods: {
      onClick: function (event) {
        this.$emit('click')
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  $height_btn: 40px;
  $height_small_btn: 35px;

  .button {
    @include btn-reset;
    border-radius: 2px;
    padding: 0 30px;
    height: $height_btn;
    line-height: $height_btn - 2px;
    text-align: center;
    transition: color .2s linear, border-color .2s linear, background-color .2s linear;

    &:disabled {
      cursor: default;
      pointer-events: none;
    }
  }

  /* ----- Sizes ----- */

  .button--small {
    height: $height_small_btn;
    line-height: $height_small_btn - 2px;
    padding: 0 25px;
  }

  /* ----- Variants ----- */

  .button--primary {
    background: $color__button;
    color: white;
    @include font-smoothing();

    &:focus,
    &:hover {
      background: $color__button--hover;
    }

    &:active {
      background: $color__button--active;
    }

    &:disabled {
      opacity: .5;
    }
  }

  .button--action,
  .button--editor {
    background: $color__action;
    color: white;
    @include font-smoothing();

    &:focus,
    &:hover {
      background: $color__action--hover;
    }

    &:active {
      background: $color__action--active;
    }

    &:disabled {
      opacity: .5;
      pointer-events: none;
    }
  }

  .button--editor {
    text-transform:uppercase;
    @include font-tiny-btn;
    font-weight:600;
    padding: 0 15px;

    .icon {
      vertical-align: baseline;
      top: 3px;
      position: relative;
      margin-right: 10px;
    }
  }

  .button--validate {
    background: $color__ok;
    color: white;
    @include font-smoothing();

    &:focus,
    &:hover {
      background: $color__ok--hover;
    }

    &:active {
      background: $color__ok--active;
    }

    &:disabled {
      color: $color__button_disabled-text;
      background: $color__button_disabled-bg;
      /*opacity: .5;*/
      pointer-events: none;
    }
  }

  .button--aslink {
    background: transparent;
    color:$color__link;

    &:hover {
      span { @include bordered($color__link, false); }
    }
  }

  .button--aslink-grey {
    @include font-small();

    background: transparent;
    color: $color__link-light;

    &:hover {
      span { @include bordered($color__link-light, false); }
    }
  }

  .button--warning {
    background: $color__error;
    color: white;
    @include font-smoothing();

    &:focus,
    &:hover {
      background: $color__error--hover;
    }

    &:active {
      background: $color__error--active;
    }

    &:disabled {
      opacity: .5;
      pointer-events: none;
    }
  }

  .button--secondary {
    background: $color__border;
    color: $color__text--forms;

    &:focus,
    &:hover {
      background: $color__border--hover;
      color: $color__text;
    }

    &:active {
      background: $color__border--focus;
      color: $color__text;
    }

    &:disabled {
      opacity: .5;
      pointer-events: none;
    }
  }

  .button--dropdown {
    border: 1px solid $color__border--hover;
    background:$color__background;
    padding-left:15px;
    padding-right:15px + 4px + 20px;

    &:after {
      top:50%;
      right:15px;
      content:'';
      position: absolute;
      display:block;
      width: 0;
      height: 0;
      margin-top: -1px;
      border-width: 4px 4px 0;
      border-style: solid;
      border-color: $color__icons transparent transparent;
    }
  }

  .button--dropdown-transparent {
    position: relative;
    border: none;
    background: transparent;
    padding-left: 15px;
    padding-right: 15px + 4px + 20px;

    &:after {
      top: 50%;
      right: 15px;
      content: '';
      position: absolute;
      display: block;
      width: 0;
      height: 0;
      margin-top: -1px;
      border-width: 4px 4px 0;
      border-style: solid;
      border-color: $color__icons transparent transparent;
    }
  }

  .button--ghost {
    height: $height_small_btn;
    line-height: $height_small_btn - 2px;
    border-radius: $height_small_btn / 2;
    background-color: transparent;
    border: 1px solid $color__border--hover;
    color: $color__text--light;
    padding: 0 20px;

    &:focus,
    &:hover {
      border-color: $color__text;
      color: $color__text;
    }

    &:active {
      border-color: $color__text;
      color: $color__text;
    }

    &:disabled {
      opacity: .5;
      pointer-events: none;
    }
  }

  /* ----- Icon buttons ----- */

  .button--icon {
    height: 26px;
    width: 26px;
    line-height: 26px - 2px;
    border-radius: 50%;
    border: 1px solid $color__fborder;
    padding: 0;
    background: $color__background;
    color: $color__icons;
    @include monospaced-figures('off'); // dont use monospaced figures here

    .icon {
      transition: color .25s linear;
    }

    &:focus,
    &:hover {
      border-color: $color__text;
      color: $color__text;

      .icon {
        color: $color__text;
      }
    }

    .icon {
      // vertical-align: top;
      // height: 100%;
      display: block;
      margin-left:auto;
      margin-right:auto;
      // margin: 0 auto;
      color: $color__icons;
    }
  }

  .button--smallIcon {
    height: 21px;
    width: 21px;
    line-height: 21px;
  }

  /* ---- Icon variants -----*/
  .button--greyed {

    &.button--icon {
      color: $color__button_greyed;
      background: $color__button_greyed--bg;

      .icon {
        color: $color__button_greyed;
      }

    }
  }

  // Buckets
  @each $current-color in $colors__bucket--list {
    $i: index($colors__bucket--list, $current-color);
    .button--icon.button--bucket--#{$i} {
      color: $current-color;
      border-color: $current-color;

      &:focus {
        color: $current-color;
        border-color: $current-color;
      }

      &:hover {
        color: $color__white;
        background-color: $current-color;
        border-color: $current-color;
      }
    }
  }
</style>
