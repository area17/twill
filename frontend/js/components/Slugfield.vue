<template>
  <div class="slug">
    <a17-textfield
      :maxlength="50"
      :label="label"
      :name="name"
      :prefix="prefix"
      :placeholder="placeholder"
      :disabled="disabled"
      :required="required"
      :readonly="readonly"
      :autofocus="autofocus"
      :error="error"
      :initialValue="value"
      @change="cleanValue"
    ></a17-textfield>
  </div>
</template>

<script>
  import InputMixin from '/src/mixins/input'
  import LocaleMixin from '/src/mixins/locale'

  export default {
    name: 'A17Slugfield',
    mixins: [InputMixin, LocaleMixin],
    props: {
      type: {
        type: String,
        default: "text"
      },
      prefix: {
        type: String,
        default: ""
      },
      initialValue: {
        default: ''
      }
    },
    data: function () {
      return {
        value: this.initialValue
      }
    },
    methods: {
      cleanValue: function(value) {
        this.value = value + "toto";
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../scss/setup/variables.scss";
  @import "../../scss/setup/colors.scss";
  @import "../../scss/setup/mixins.scss";
</style>
