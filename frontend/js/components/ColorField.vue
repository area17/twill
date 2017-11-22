<template>
  <a17-inputframe :error="error" :note="note" :label="label">
    <div class="form__field" :class="textfieldClasses">
      <input
        type="text"
        :placeholder="placeholder"
        :name="name"
        :id="name"
        :disabled="disabled"
        :required="required"
        :readonly="readonly"
        :autofocus="autofocus"
        :autocomplete="autocomplete"
        :value="color"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
      />
      <span v-if="color !== ''" class="form__field--color" :style="bcgStyle"></span>
    </div>
    <a17-colorpicker :color="color" @change="updateColor"></a17-colorpicker>
  </a17-inputframe>
</template>

<script>
  import InputMixin from '@/mixins/input'
  import InputframeMixin from '@/mixins/inputFrame'
  import ColorPicker from '@/components/ColorPicker'

  export default {
    name: 'a17ColorField',
    mixins: [InputMixin, InputframeMixin],
    components: {
      'a17-colorpicker': ColorPicker
    },
    data: function () {
      return {
        color: '#256817'
      }
    },
    computed: {
      bcgStyle: function () {
        return {
          'background-color': this.color !== '' ? this.color : 'transparent'
        }
      },
      textfieldClasses: function () {
        return {
          's--focus': this.focused,
          's--disabled': this.disabled
        }
      }
    },
    methods: {
      onBlur: function (event) {
        console.log(event)
        const newValue = event.target.value
        this.color = newValue
      },
      onFocus: function () {
      },
      onInput: function () {
      },
      updateColor: function (color) {
        this.color = color
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .form__field {
    display: flex;
    align-items: center;
    padding: 0 15px;

    input {
      padding: 0;
    }

    .form__field--color {
      display: block;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      border: 1px solid $color__border--hover;
      transition: background 250ms;
    }
  }
</style>
