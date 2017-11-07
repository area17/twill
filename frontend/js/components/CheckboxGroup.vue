<template>
  <a17-inputframe :error="error" :note="note" :label="label">
    <ul class="checkboxGroup">
      <li class="checkboxGroup__item" v-for="checkbox in options">
        <a17-checkbox :name="name" :value="checkbox.value" :label="checkbox.label" @change="changeValue" :initialValue="currentValue" :disabled="checkbox.disabled || disabled"></a17-checkbox>
      </li>
    </ul>
  </a17-inputframe>
</template>

<script>
  import InputframeMixin from '@/mixins/inputFrame'
  import CheckboxMixin from '@/mixins/checkboxes'

  export default {
    name: 'A17CheckboxGroup',
    mixins: [InputframeMixin, CheckboxMixin],
    methods: {
      changeValue: function (newValue) {
        this.currentValue = newValue

        this.$emit('change', this.currentValue)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../scss/setup/variables.scss";
  @import "../../scss/setup/colors.scss";
  @import "../../scss/setup/mixins.scss";

  .checkboxGroup {
    color:$color__text;
  }

  .checkboxGroup__item {
    padding:7px 0 8px 0;
  }
</style>
