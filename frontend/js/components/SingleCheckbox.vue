<template>
  <a17-inputframe :error="error" :note="note" :name="name">
    <div class="singleCheckbox">
      <span class="checkbox">
        <input type="checkbox" class="checkbox__input" :class="checkboxClasses" value="true" :name="name + '[' + randKey + ']'" :id="uniqId" :disabled="disabled">
        <label class="checkbox__label" :for="uniqId" @click.prevent="changeCheckbox">{{ label }} <span class="checkbox__icon"><span v-svg symbol="check"></span></span></label>
      </span>
    </div>
  </a17-inputframe>
</template>

<script>
  import randKeyMixin from '@/mixins/randKey'
  import InputframeMixin from '@/mixins/inputFrame'
  import FormStoreMixin from '@/mixins/formStore'

  export default {
    name: 'A17SingleCheckbox',
    mixins: [randKeyMixin, InputframeMixin, FormStoreMixin],
    props: {
      name: {
        type: String,
        default: ''
      },
      initialValue: {
        type: Boolean,
        default: true // bold
      },
      theme: {
        type: String,
        default: '' // bold
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
      uniqId: function () {
        return this.name + '_' + this.randKey
      },
      checkboxClasses: function () {
        return [
          this.theme ? `checkbox__input--${this.theme}` : '',
          this.checkedValue ? 'checkbox__input--checked' : ''
        ]
      },
      checkedValue: {
        get: function () {
          return this.currentValue
        },
        set: function (value) {
          if (value !== this.currentValue) {
            this.currentValue = value
            if (typeof this.saveIntoStore !== 'undefined') this.saveIntoStore(value)
            this.$emit('change', value)
          }
        }
      }
    },
    methods: {
      updateFromStore: function (newValue) { // called from the formStore mixin
        this.checkedValue = newValue
      },
      changeCheckbox: function () {
        this.checkedValue = !this.checkedValue
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .checkbox {
    color:$color__text;
    min-width:30px;
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
    opacity:0;
  }

  .checkbox__label {
    display: block;
    position: relative;
    padding-left: 15px + 10px;
    color: $color__f--text;
    cursor: pointer;
  }

  .checkbox__icon,
  .checkbox__label::before {
    position: absolute;
    left: 0;
    top: 2px;
    width: 15px;
    height: 15px;
    border-radius: 2px;
    transition: all .2s linear;
  }

  .checkbox__label::before {
    content: '';
    background-color: $color__f--bg;
    border: 1px solid $color__fborder;
  }

  .checkbox__icon {
    background-color: $color__fborder--active;
    color:$color__background;
    opacity: 0;
  }

  .checkbox__icon .icon {
    color:$color__background;
    top: 2px;
    position: relative;
    display: block;
    margin-left:auto;
    margin-right:auto;
  }

  .checkbox__input:focus + .checkbox__label::before,
  .checkbox__label:hover::before {
    border-color: $color__fborder--hover;
  }

  // .checkbox__input:checked + .checkbox__label,
  .checkbox__label:hover,
  .checkbox__input:hover   + .checkbox__label,
  .checkbox__input--checked + .checkbox__label {
    color:$color__text;
  }

  // .checkbox__input:checked + .checkbox__label .checkbox__icon,
  .checkbox__input--checked + .checkbox__label .checkbox__icon {
    opacity: 1;
  }

  /* disabled state */
  .checkbox__input:disabled + .checkbox__label {
    opacity: .33;
    pointer-events: none;
  }

  // .checkbox__input:checked:disabled + .checkbox__label,
  .checkbox__input--checked:disabled + .checkbox__label {
    opacity: .66;
    pointer-events: none;
  }

  /* Green variant */
  .checkbox__input--bold + .checkbox__label .checkbox__icon {
    background-color:$color__publish
  }

  /* Minus variant (for Bulk Edit) */
  .checkbox--minus {
    .checkbox__label::after {
      content:'';
      display:block;
      width:9px;
      height:2px;
      background-color:$color__fborder--active;
      position:absolute;
      left:2px + 15px - 14px;
      top:8px;
    }

    // .checkbox__input:checked + .checkbox__label .checkbox__icon,
    .checkbox__input--checked + .checkbox__label .checkbox__icon {
      opacity: 0;
    }
  }
</style>
