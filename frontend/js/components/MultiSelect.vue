<template>
  <div class="multiselectorOuter">
    <a17-inputframe :error="error" :note="note" :label="label" :name="name" :add-new="addNew">
      <div class="multiselector" :class="gridClasses">
        <div class="multiselector__outer">
          <div class="multiselector__item"
               v-for="(checkbox, index) in fullOptions"
               :key="index"
               :style="itemStyle"
            >
            <input class="multiselector__checkbox" :class="{'multiselector__checkbox--checked': checkedValue.includes(checkbox.value) }" type="checkbox" :value="checkbox.value" :name="name +   '[' + randKey + ']'" :id="uniqId(checkbox.value, index)" :disabled="checkbox.disabled || disabled" v-model="checkedValue">
            <label class="multiselector__label" :for="uniqId(checkbox.value, index)" @click.prevent="changeCheckbox(checkbox.value)">
              <span class="multiselector__icon"><span v-svg symbol="check"></span></span>
              {{ checkbox.label }}
            </label>
            <span class="multiselector__bg"></span>
          </div>
        </div>
      </div>
    </a17-inputframe>
    <template v-if="addNew">
      <a17-modal-add ref="addModal" :name="name" :form-create="addNew" :modal-title="'Add new ' + label">
        <slot name="addModal"></slot>
      </a17-modal-add>
    </template>
  </div>
</template>

<script>
  import isEqual from 'lodash/isEqual'

  import AttributesMixin from '@/mixins/addAttributes'
  import CheckboxMixin from '@/mixins/checkboxes'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import randKeyMixin from '@/mixins/randKey'

  export default {
    name: 'A17Multiselect',
    mixins: [randKeyMixin, InputframeMixin, CheckboxMixin, FormStoreMixin, AttributesMixin],
    props: {
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
        default: false
      },
      border: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      gridClasses: function () {
        if (this.columns >= 1) {
          return [
            'multiselector--columns',
            this.grid ? 'multiselector--grid' : ''
          ]
        }

        return [
          this.grid ? 'multiselector--grid' : '',
          this.inline ? 'multiselector--inline' : '',
          this.border ? 'multiselector--border' : ''
        ]
      },
      itemStyle: function () {
        if (this.columns >= 1) {
          return {
            width: `${100 / this.columns}%`
          }
        }

        return {}
      }
    },
    methods: {
      updateFromStore: function (newValue) { // called from the formStore mixin
        if (!isEqual(newValue, this.checkedValue)) {
          this.checkedValue = newValue
        }
      },
      changeCheckbox: function (newValue) {
        const isChecked = this.checkedValue.indexOf(newValue)
        const newCheckedValue = this.checkedValue.slice()

        // remove or add
        if (isChecked > -1) newCheckedValue.splice(isChecked, 1)
        else newCheckedValue.push(newValue)

        // check min or max here to avoid unecessary commits
        const isMax = this.isMax(newCheckedValue)
        const isMin = this.isMin(newCheckedValue)
        if (isMax || isMin) return

        this.checkedValue = newCheckedValue
      },
      uniqId: function (value, index) {
        return this.name + '_' + value + '-' + (this.randKey * (index + 1))
      }
    }
  }
</script>

<style lang="scss" scoped>
  $checkboxSize: 15px;

  .multiselector {
    color:$color__text;
  }

  .multiselector__outer {
    display:block;
  }

  .multiselector__checkbox {
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

  .multiselector__label {
    display: block;
    position: relative;
    color: $color__f--text;
    cursor: pointer;
    z-index:1;
    padding-left: 15px + 10px;
    padding-right:5px;
  }

  .multiselector__bg {
    display:none;
  }

  .multiselector__icon {
    display:block;
    position:absolute;
    left: 0;
    top: 2px;
    width: $checkboxSize;
    height: $checkboxSize;
    border: 1px solid $color__fborder;
    background: $color__f--bg;
    border-radius: 2px;
    transition: all .25s $bezier__bounce;

    .icon {
      color:$color__background;
      top: 1px;
      position: relative;
      line-height:11px;
      display: block;
      margin-left:auto;
      margin-right:auto;
    }
  }

  .multiselector__item {
    padding:7px 0 8px 0;
  }

  .multiselector__label:hover .multiselector__icon,
  .multiselector__label:focus .multiselector__icon {
    border-color: $color__fborder--hover;
  }

  // .multiselector__checkbox:checked + .multiselector__label
  .multiselector__label:hover,
  .multiselector__checkbox:hover   + .multiselector__label,
  .multiselector__checkbox:focus   + .multiselector__label,
  .multiselector__checkbox--checked + .multiselector__label {
    color:$color__text;
  }

  .multiselector__checkbox:disabled + .multiselector__label {
    opacity: .5;
    pointer-events: none;
  }

  .multiselector__checkbox:focus + .multiselector__label .multiselector__icon {
    border-color: $color__border--focus;
  }

  // .multiselector__checkbox:checked
  .multiselector__checkbox:hover,
   .multiselector__checkbox--checked{
    + .multiselector__label + .multiselector__bg {
      background-color:$color__ultralight;
    }
  }

  //.multiselector__checkbox:checked + .multiselector__label .multiselector__icon,
  .multiselector__checkbox--checked + .multiselector__label .multiselector__icon {
    border-color: $color__fborder--active;
    background-color: $color__fborder--active;
  }

  // .multiselector__checkbox:focus:checked + .multiselector__label .multiselector__icon,
  .multiselector__checkbox--checked:focus + .multiselector__label .multiselector__icon {
    border-color: $color__fborder--active;
  }

  /* grid + columns shared styles */
  .multiselector--grid,
  .multiselector--columns {
    border:1px solid $color__border;
    background-clip: padding-box;
    box-sizing: border-box;
    overflow:hidden;
    border-radius:2px;

    .multiselector__outer {
      display: flex;
      flex-direction: row;
      flex-wrap:wrap;
      box-sizing: border-box;
      overflow:hidden;
      margin-bottom: -1px;
      margin-right: -1px;
    }

    .multiselector__item {
      width:100%;
      height:50%;
      border-right:1px solid $color__border--light;
      border-bottom:1px solid $color__border--light;
      overflow: hidden;
      position:relative;
      padding:0;

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

    .multiselector__label {
      height:50px;
      line-height: 50px;
      padding-left: 30px + 12px;
      color: $color__text--light;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow:hidden;
    }

    .multiselector__icon {
      left:15px;
      top:50%;
      margin-top:-8px;
    }
  }

  /* grid version */
  .multiselector--grid {
    .multiselector__bg {
      display:block;
      position:absolute;
      top:0;
      left:0;
      right:0;
      bottom:0;
      z-index:0;
      background-color:$color__background;
      transition: background-color .25s $bezier__bounce;
    }

    //.multiselector__checkbox:checked + .multiselector__label .multiselector__icon,
    .multiselector__checkbox--checked + .multiselector__label .multiselector__icon {
      border-color: $color__fborder--active;
      background-color: $color__fborder--active;
    }

    // .multiselector__checkbox:focus:checked + .multiselector__label .multiselector__icon,
    .multiselector__checkbox--checked:focus + .multiselector__label .multiselector__icon {
      border-color: $color__fborder--active;
    }
  }

  /* grid or columns in editor */
  .s--in-editor .multiselector--grid .multiselector__item,
  .s--in-editor .multiselector--columns .multiselector__item {
    width: 100% !important; // override inline styles, if any (@see itemStyle)
  }

  /* inline version */
  .multiselector--inline .multiselector__outer {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    overflow: hidden;
  }

  .multiselector--inline .multiselector__item {
    margin-right:20px;
  }

  /* border version */
  .multiselector--border {
    border: 1px solid $color__border;
    background-clip: padding-box;
    box-sizing: border-box;
    overflow: hidden;
    border-radius: 2px;
    padding: 7px 15px;
  }

  .multiselector--border.multiselector--inline {
    padding: 0 15px;

    .multiselector__outer {
      box-sizing: border-box;
      overflow: hidden;
      margin-bottom: -1px;
      margin-right: -1px;
    }

    .multiselector__item {
      padding: 0;
      height: 50%;
      overflow: hidden;
      position: relative;
    }

    .multiselector__label {
      padding-left: 25px;
      height: 50px;
      line-height: 50px;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;

      .multiselector__icon {
        top: 50%;
        margin-top: -9px;
      }
    }
  }
</style>
