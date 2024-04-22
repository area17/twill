<template>
  <a17-inputframe :error="error" :note="note" :label="label" :name="name">
    <ul class="checkboxGroup" :class="checkboxClasses">
      <li class="checkboxGroup__item" v-for="checkbox in options" :key="checkbox.value">
        <a17-checkbox :name="name" :value="checkbox.value" :label="checkbox.label" @change="changeValue" :initialValue="currentValue" :disabled="checkbox.disabled || disabled"></a17-checkbox>
      </li>
    </ul>
  </a17-inputframe>
</template>

<script>
  import isEqual from 'lodash/isEqual'

  import CheckboxMixin from '@/mixins/checkboxes'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'

  export default {
    name: 'A17CheckboxGroup',
    props: {
      name: {
        type: String,
        default: ''
      },
      inline: {
        type: Boolean,
        default: false
      },
      options: {
        type: Array,
        default: function () { return [] }
      }
    },
    mixins: [InputframeMixin, CheckboxMixin, FormStoreMixin],
    computed: {
      checkboxClasses: function () {
        return [
          this.inline ? 'checkboxGroup--inline' : ''
        ]
      }
    },
    methods: {
      formatValue: function (newVal, oldval) {
        const self = this
        if (!newVal) return
        if (!oldval) return

        const isMax = this.isMax(newVal) // defined in the checkboxes mixin
        const isMin = this.isMin(newVal) // defined in the checkboxes mixin

        if (isMax || isMin) {
          if (!isEqual(oldval, self.checkedValue)) {
            self.checkedValue = oldval
          }
        }
      },
      updateFromStore: function (newValue) { // called from the formStore mixin
        this.updateValue(newValue)
      },
      updateValue: function (newValue) {
        this.checkedValue = newValue
      },
      changeValue: function (newValue) {
        if (!isEqual(newValue, this.currentValue)) {
          this.updateValue(newValue)
        }
      }
    },
    mounted: function () {
      if ((this.max + this.min) > 0) {
        this.$watch('currentValue', this.formatValue, {
          immediate: true
        })
      }
    }
  }
</script>

<style lang="scss" scoped>

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
