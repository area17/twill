<template>
  <span class="checkbox">
    <input
      type="checkbox"
      :key="uniqId"
      class="checkbox__input"
      :class="checkboxClasses"
      :value="value"
      :name="name"
      :id="uniqId"
      :disabled="disabled"
      v-model="checkedValue"
    />
    <label
      class="checkbox__label"
      :for="uniqId"
      :style="color ? `--swatch:${color}` : null"
      >{{ label }}
      <span v-if="count" class="checkbox__count f--small">{{ count }}</span>
      <span class="checkbox__icon">
        <span v-svg symbol="check"></span>
      </span>
    </label>
  </span>
</template>

<script>
  import randKeyMixin from '@/mixins/randKey'

  export default {
    name: 'A17Checkbox',
    mixins: [randKeyMixin],
    props: {
      value: {
        default: ''
      },
      initialValue: {
        default: function() {
          return []
        }
      },
      name: {
        type: String,
        default: ''
      },
      theme: {
        type: String,
        default: '' // bold
      },
      label: {
        type: String,
        default: ''
      },
      disabled: {
        type: Boolean,
        default: false
      },
      count: {
        type: [String, Number],
        default: ''
      },
      color: {
        type: String,
        default: null
      }
    },
    computed: {
      uniqId: function(value) {
        return this.name + '_' + this.value + '-' + this.randKey
      },
      checkboxClasses: function() {
        return [this.theme ? `checkbox__input--${this.theme}` : '']
      },
      checkedValue: {
        get: function() {
          return this.initialValue
        },
        set: function(value) {
          this.$emit('change', value)
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  .checkbox {
    color: $color__text;
    min-width: 30px;
  }

  .checkbox__input {
    position: absolute;
    width: 1px;
    height: 1px;
    margin-top: -1px;
    margin-left: -1px;
    padding: 0;
    border: 0 none;
    clip: rect(1px, 1px, 1px, 1px);
    overflow: hidden;
    opacity: 0;
  }

  .checkbox__label {
    display: flex;
    position: relative;
    padding-left: 15px + 10px;
    color: $color__f--text;
    cursor: pointer;
    justify-content: space-between;
    flex-shrink: 0;
  }

  .checkbox__count {
    flex-shrink: 0;
  }

  .checkbox__icon,
  .checkbox__label::before {
    position: absolute;
    left: 0;
    top: 2px;
    width: 15px;
    height: 15px;
    border-radius: 2px;
    transition: all 0.2s linear;
  }

  .checkbox__label::before {
    content: '';
    background-color: $color__f--bg;
    border: 1px solid $color__fborder;
  }

  .checkbox__icon {
    background-color: $color__fborder--active;
    color: $color__background;
    opacity: 0;
  }

  .checkbox__icon .icon {
    color: $color__background;
    top: 2px;
    position: relative;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }

  .checkbox__input:focus + .checkbox__label::before,
  .checkbox__label:hover::before {
    border-color: $color__fborder--hover;
  }

  .checkbox__label:hover,
  .checkbox__input:hover + .checkbox__label,
  .checkbox__input:checked + .checkbox__label {
    color: $color__text;
  }

  .checkbox__input:checked + .checkbox__label .checkbox__icon {
    opacity: 1;
  }

  /* disabled state */
  .checkbox__input:disabled + .checkbox__label {
    opacity: 0.33;
    pointer-events: none;
  }

  .checkbox__input:checked:disabled + .checkbox__label {
    opacity: 0.66;
    pointer-events: none;
  }

  /* Green variant */
  .checkbox__input--bold + .checkbox__label .checkbox__icon {
    background-color: $color__publish;
  }

  /* Minus variant (for Bulk Edit) */
  .checkbox--minus {
    .checkbox__label::after {
      content: '';
      display: block;
      width: 9px;
      height: 2px;
      background-color: $color__fborder--active;
      position: absolute;
      left: 2px + 15px - 14px;
      top: 8px;
    }

    .checkbox__input:checked + .checkbox__label .checkbox__icon {
      opacity: 0;
    }
  }

  .checkbox__label[style*='--swatch'] {
    padding-right: rem-calc(24);

    &::after {
      content: '';
      position: absolute;
      width: rem-calc(16);
      height: rem-calc(16);
      border-radius: rem-calc(2);
      right: 0;
      top: 2px;
      background-color: var(--swatch);
    }
  }

  .checkbox__label[style*='--swatch: #FFFFFF;']::after {
    border: 1px solid $color__fborder;
  }
</style>
