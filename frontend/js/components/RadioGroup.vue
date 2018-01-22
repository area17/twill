<template>
  <a17-inputframe :error="error" :note="note" :label="label" :name="name">
    <ul class="radioGroup" :class="radioClasses">
      <li class="radioGroup__item" v-for="(radio, index) in radios">
        <a17-radio :customClass="'radio__' + radioClass + '--' + (index + 1)" :name="name" :value="radio.value" :label="radio.label" @change="changeValue" :initialValue="currentValue" :disabled="radio.disabled"></a17-radio>
      </li>
    </ul>
  </a17-inputframe>
</template>

<script>
  import InputframeMixin from '@/mixins/inputFrame'
  import FormStoreMixin from '@/mixins/formStore'

  export default {
    name: 'A17CheckboxGroup',
    mixins: [InputframeMixin, FormStoreMixin],
    props: {
      radioClass: {
        type: String,
        default: ''
      },
      inline: {
        type: Boolean,
        default: false
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
    computed: {
      radioClasses: function () {
        return [
          this.inline ? `radioGroup--inline` : ''
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
        this.updateValue(newValue)

        this.$emit('change', this.currentValue)
        this.saveIntoStore(newValue)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .radioGroup {
    color:$color__text;
  }

  .radioGroup--inline {
    display:flex;
    flex-flow: row wrap;
    overflow:hidden;
  }

  .radioGroup--inline .radioGroup__item {
    margin-right:20px;
  }

  .radioGroup__item {
    padding:7px 0 8px 0;
  }
</style>
