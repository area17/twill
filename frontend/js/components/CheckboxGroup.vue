<template>
  <a17-inputframe :error="error" :note="note" :label="label" :name="name">
    <ul class="checkboxGroup" :class="checkboxClasses">
      <li class="checkboxGroup__item" v-for="checkbox in options">
        <a17-checkbox :name="name" :value="checkbox.value" :label="checkbox.label" @change="changeValue" :initialValue="currentValue" :disabled="checkbox.disabled || disabled"></a17-checkbox>
      </li>
    </ul>
  </a17-inputframe>
</template>

<script>
  import { isEqual } from 'lodash'
  import InputframeMixin from '@/mixins/inputFrame'
  import CheckboxMixin from '@/mixins/checkboxes'
  import FormStoreMixin from '@/mixins/formStore'

  export default {
    name: 'A17CheckboxGroup',
    props: {
      inline: {
        type: Boolean,
        default: false
      }
    },
    mixins: [InputframeMixin, CheckboxMixin, FormStoreMixin],
    computed: {
      checkboxClasses: function () {
        return [
          this.inline ? `checkboxGroup--inline` : ''
        ]
      }
    },
    methods: {
      updateFromStore: function (newValue) { // called from the formStore mixin
        this.updateValue(newValue)
      },
      updateValue: function (newValue) {
        this.currentValue = newValue
      },
      changeValue: function (newValue) {
        if (!isEqual(newValue, this.currentValue)) {
          this.updateValue(newValue)
          this.$emit('change', this.currentValue)
          this.saveIntoStore(newValue)
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .checkboxGroup {
    color:$color__text;
  }

  .checkboxGroup--inline {
    display:flex;
    flex-flow: row wrap;
    overflow:hidden;
  }

  .checkboxGroup--inline .checkboxGroup__item {
    margin-right:20px;
  }

  .checkboxGroup__item {
    padding:7px 0 8px 0;
  }
</style>
