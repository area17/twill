<template>
  <span class="radio" :class="customClass">
    <input type="radio" class="radio__input" :value="value" :name="name" :id="name + '_' + value" :disabled="disabled" v-model="selectedValue">
    <label class="radio__label" :for="name + '_' + value">{{ label }}</label>
  </span>
</template>

<script>
  export default {
    name: 'A17Radio',
    props: {
      customClass: {
        type: String,
        default: ''
      },
      value: {
        default: ''
      },
      name: {
        type: String,
        default: ''
      },
      label: {
        type: String,
        default: ''
      },
      initialValue: {
        default: ''
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },
    data: function () {
      return {
        currentValue: this.initialValue
      }
    },
    computed: {
      selectedValue: {
        get: function () {
          return this.currentValue
        },
        set: function (value) {
          this.currentValue = value
          this.$emit('change', value)
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../scss/setup/variables.scss";
  @import "../../scss/setup/colors.scss";
  @import "../../scss/setup/mixins.scss";

  .radio {
    color:$color__text;
  }

  .radio__input {
    position: absolute;
    width: 1px;
    height: 1px;
    margin-top: -1px;
    margin-left: -1px;
    padding: 0;
    border: 0 none;
    clip: rect(1px, 1px, 1px, 1px);
    overflow: hidden;
  }

  .radio__label {
    display: block;
    position: relative;
    padding-left: 18px + 15px;
    color: $color__f--text;
    cursor: pointer;
  }

  .radio__label::before,
  .radio__label::after {
    content: '';
    position: absolute;
    left: 0;
    top: 1px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    transition: all .25s $bezier__bounce;
  }

  .radio__label::before {
    border: 1px solid $color__fborder;
    background-color: $color__f--bg;
  }

  .radio__label::after {
    border: 0 none;
    background-color: $color__icons;
    opacity:0;
    transform: scale(.1);
  }

  .radio__label:hover::before {
    border-color: $color__fborder--hover;
  }

  .radio__label:hover,
  .radio__input:hover   + .radio__label,
  .radio__input:checked + .radio__label {
    color:$color__text;
  }

  .radio__input:focus + .radio__label::before {
    border-color: $color__border--focus;
  }

  .radio__input:checked + .radio__label {
    color:$color__text;
  }

  .radio__input:focus:checked + .radio__label::before,
  .radio__input:checked + .radio__label::before {
    border-color: $color__fborder--active;
    background-color: $color__fborder--active;
  }

  .radio__input:checked + .radio__label::after {
    opacity: 1;
    transform: scale(.33);
    background-color: $color__background;
  }

  .radio__input:disabled + .radio__label {
    opacity: .5;
    pointer-events: none;
  }

  /* custom radios buttons */

  @each $current-color in $colors__bucket--list {
    $i: index($colors__bucket--list, $current-color);
    .radio__bucket--#{$i} {

      .radio__input:hover + .radio__label::after {
        opacity: 1;
        transform: scale(.33);
        background-color: $color__background;
      }

      .radio__input:hover + .radio__label::before,
      .radio__input:focus:checked + .radio__label::before,
      .radio__input:checked + .radio__label::before {
        border-color: $current-color;
        background-color: $current-color;
      }
    }
  }
</style>
