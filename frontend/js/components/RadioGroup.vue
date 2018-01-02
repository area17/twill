<template>
  <a17-inputframe :error="error" :note="note" :label="label" :name="name">
    <ul class="radioGroup">
      <li class="radioGroup__item" v-for="(radio, index) in radios">
        <a17-radio :customClass="'radio__' + radioClass + '--' + (index + 1)" :name="name" :value="radio.value" :label="radio.label" @change="changeValue" :initialValue="currentValue" :disabled="radio.disabled"></a17-radio>
      </li>
    </ul>
  </a17-inputframe>
</template>

<script>
  import InputframeMixin from '@/mixins/inputFrame'

  export default {
    name: 'A17CheckboxGroup',
    mixins: [InputframeMixin],
    props: {
      radioClass: {
        type: String,
        default: ''
      },
      name: {
        type: String,
        default: ''
      },
      label: {
        default: ''
      },
      initialValue: {
        default: ''
      },
      radios: {
        default: function () { return [] }
      }
    },
    data: function () {
      return {
        currentValue: this.initialValue
      }
    },
    methods: {
      changeValue: function (newValue) {
        this.currentValue = newValue

        this.$emit('change', this.currentValue)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .radioGroup {
    color:$color__text;
  }

  .radioGroup__item {
    padding:7px 0 8px 0;
  }
</style>
