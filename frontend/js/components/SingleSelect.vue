<template>
  <div :class="outerClasses">
    <a17-inputframe :error="error" :note="note" :label="label" :name="name" :add-new="addNew" :variant="variantInput">
      <input type="hidden" :name="name" v-model="value" />
      <div class="singleselector" :class="gridClasses">
        <div class="singleselector__outer">
          <div class="singleselector__item"
               v-for="(radio, index) in fullOptions"
               :key="index"
               :style="itemStyle"
          >
            <input class="singleselector__radio" type="radio" :value="radio.value" :name="name + '[' + randKey + ']'" :id="uniqId(radio.value, index)" :disabled="radio.disabled || disabled" :class="{'singleselector__radio--checked': radio.value == selectedValue }">
            <label class="singleselector__label" :for="uniqId(radio.value, index)" @click.prevent="changeRadio(radio.value)">{{ radio.label }}</label>
            <span class="singleselector__bg"></span>
          </div>
        </div>
      </div>
    </a17-inputframe>
    <template v-if="addNew">
      <a17-modal-add ref="addModal" :name="name" :form-create="addNew" :modal-title="'Add new ' + label">
        <slot name="addModal"></slot>
      </a17-modal-add>
    </template>
    <template v-if="requireConfirmation">
      <a17-dialog ref="warningConfirm" modal-title="Confirm" confirm-label="Confirm">
        <p class="modal--tiny-title"><strong>{{ confirmTitleText }}</strong></p>
        <p>{{ confirmMessageText }}</p>
      </a17-dialog>
    </template>
  </div>
</template>

<script>
  import AttributesMixin from '@/mixins/addAttributes'
  import ConfirmationMixin from '@/mixins/confirmationMixin'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import randKeyMixin from '@/mixins/randKey'

  export default {
    name: 'A17Singleselect',
    mixins: [randKeyMixin, InputframeMixin, FormStoreMixin, AttributesMixin, ConfirmationMixin],
    props: {
      name: {
        type: String,
        default: ''
      },
      grid: {
        type: Boolean,
        default: true
      },
      columns: {
        type: Number,
        default: 0
      },
      inline: {
        type: Boolean,
        default: true
      },
      inTable: {
        type: Boolean,
        default: false
      },
      border: {
        type: Boolean,
        default: false
      },
      selected: {
        default: ''
      },
      options: {
        default: function () { return [] }
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },
    data: function () {
      return {
        value: this.selected
      }
    },
    computed: {
      variantInput: function () {
        return [
          this.inTable ? 'intable' : ''
        ].join(' ')
      },
      outerClasses: function () {
        return [
          'multiselectorOuter'
        ]
      },
      gridClasses: function () {
        if (this.columns >= 1) {
          return [
            'singleselector--columns',
            this.grid ? 'singleselector--grid' : ''
          ]
        }

        return [
          this.grid ? 'singleselector--grid' : '',
          this.inline ? 'singleselector--inline' : '',
          this.border ? 'singleselector--border' : ''
        ]
      },
      itemStyle: function () {
        if (this.columns >= 1) {
          return {
            width: `${100 / this.columns}%`
          }
        }

        return {}
      },
      selectedValue: {
        get: function () {
          return this.value
        },
        set: function (value) {
          if (value !== this.value) {
            this.value = value

            // see formStore mixin
            this.saveIntoStore(value)
            this.$emit('change', value)
          }
        }
      }
    },
    methods: {
      updateFromStore: function (newValue) { // called from the formStore mixin
        if (newValue !== this.value) {
          this.value = newValue
        }
      },
      changeRadio: function (value) {
        if (this.requireConfirmation) {
          this.$refs.warningConfirm.open(() => {
            this.selectedValue = value
          })
        } else {
          this.selectedValue = value
        }
      },
      uniqId: function (value, index) {
        return this.name + '_' + value + '-' + (this.randKey * (index + 1))
      }
    }
  }
</script>

<style lang="scss" scoped>
  .singleselector {
    color:$color__text;
  }

  .singleselector__radio {
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

  .singleselector__label {
    display: block;
    position: relative;
    padding-left: 15px + 10px;
    color: $color__f--text;
    cursor: pointer;
    z-index:1;
    padding-right:5px;
  }

  .singleselector__bg {
    display:none;
  }

  .singleselector__item {
    padding:7px 0 8px 0;
  }

  .singleselector__label::before,
  .singleselector__label::after {
    content: '';
    position: absolute;
    left: 0;
    top: 1px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    transition: all .25s $bezier__bounce;
  }

  .singleselector__label::before {
    border: 1px solid $color__fborder;
    background-color: $color__f--bg;
  }

  .singleselector__label::after {
    border: 0 none;
    background-color: $color__icons;
    opacity:0;
    transform: scale(.1);
  }

  .singleselector__label:hover::before,
  .singleselector__radio:focus + .singleselector__label::before {
    border-color: $color__fborder--hover;
  }

  // .singleselector__radio:checked  + .singleselector__label,
  .singleselector__label:hover,
  .singleselector__radio:hover    + .singleselector__label,
  .singleselector__radio:focus    + .singleselector__label,
  .singleselector__radio--checked + .singleselector__label {
    color:$color__text;
  }

  // .singleselector__radio:checked + .singleselector__label::after,
  .singleselector__radio--checked + .singleselector__label::after {
    opacity: 1;
    transform: scale(.33);
    background-color: $color__background;
  }

  .singleselector__radio:disabled + .singleselector__label {
    opacity: .5;
    pointer-events: none;
  }

  .singleselector__radio:focus + .singleselector__label::before {
    border-color: $color__border--focus;
  }

  // .singleselector__radio:checked,
  .singleselector__radio:hover,
  .singleselector__radio--checked {
    + .singleselector__label + .singleselector__bg {
      background:$color__ultralight;
    }
  }

  // .singleselector__radio:checked + .singleselector__label::before,
  .singleselector__radio--checked + .singleselector__label::before {
    border-color: $color__fborder--active;
    background-color: $color__fborder--active;
  }

  // .singleselector__radio:focus:checked + .singleselector__label::before,
  .singleselector__radio--checked:focus + .singleselector__label::before {
    border-color: $color__fborder--active;
  }

  /* grid + columns shared styles */
  .singleselector--grid,
  .singleselector--columns {
    border:1px solid $color__border;
    background-clip: padding-box;
    box-sizing: border-box;
    overflow:hidden;
    border-radius:2px;

    .singleselector__outer {
      display: flex;
      flex-direction: row;
      flex-wrap:wrap;
      box-sizing: border-box;
      overflow:hidden;
      margin-bottom: -1px;
      margin-right: -1px;
    }

    .singleselector__item {
      padding:0;
      width:100%;
      height:50%;
      border-right:1px solid $color__border--light;
      border-bottom:1px solid $color__border--light;
      overflow: hidden;
      position:relative;

      @include breakpoint('small') {
        width:33.3333%;
      }

      @include breakpoint('medium') {
        width:100%;
      }

      @include breakpoint('large') {
        width:33.3333%;
      }

      @include breakpoint('large+') {
        width:25%;
      }
    }

    .singleselector__label {
      padding-left: 15px + 18px + 10px;
      color: $color__text--light;
      height:50px;
      line-height:50px;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow:hidden;
    }

    .singleselector__label::before,
    .singleselector__label::after {
      left: 15px;
      top: 50%;
      margin-top:-9px;
    }
  }

  /* grid version */
  .singleselector--grid {
    .singleselector__bg {
      display:block;
      position:absolute;
      top:0;
      left:0;
      right:0;
      bottom:0;
      z-index:0;
      background:$color__background;
      transition: background-color .25s $bezier__bounce;
    }

    // .singleselector__radio:checked + .singleselector__label::before,
    .singleselector__radio--checked + .singleselector__label::before {
      border-color: $color__fborder--active;
      background-color: $color__fborder--active;
    }

    // .singleselector__radio:focus:checked + .singleselector__label::before,
    .singleselector__radio--checked:focus + .singleselector__label::before {
      border-color: $color__fborder--active;
    }
  }

  /* grid or columns in editor */
  .s--in-editor .singleselector--grid .singleselector__item,
  .s--in-editor .singleselector--columns .singleselector__item {
    width: 100% !important; // override inline styles, if any (@see itemStyle)
  }

  /* inline version */
  .singleselector--inline .singleselector__outer {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    overflow: hidden;
  }

  .singleselector--inline .singleselector__item {
    margin-right:20px;
  }

  /* border version */
  .singleselector--border {
    border: 1px solid $color__border;
    background-clip: padding-box;
    box-sizing: border-box;
    overflow: hidden;
    border-radius: 2px;
    padding: 7px 15px;
  }

  .singleselector--border.singleselector--inline {
    padding: 0 15px;

    .singleselector__outer {
      box-sizing: border-box;
      overflow: hidden;
      margin-bottom: -1px;
      margin-right: -1px;
    }

    .singleselector__item {
      padding: 0;
      height: 50%;
      overflow: hidden;
      position: relative;
    }

    .singleselector__label {
      padding-left: 25px;
      height: 50px;
      line-height: 50px;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
    }

    .singleselector__label::before,
    .singleselector__label::after {
      top: 50%;
      margin-top: -9px;
    }
  }
</style>
