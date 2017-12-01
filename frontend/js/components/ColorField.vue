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
        :value="value"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
        maxlength="7"
      />
      <a17-dropdown ref="colorDropdown" class="form__field--color" position="bottom-right" :arrow="true" :offset="15" :minWidth="300" :clickable="true" :sideOffset="15">
        <span class="form__field--colorBtn" :style="bcgStyle" @click="$refs.colorDropdown.toggle()"></span>
        <div slot="dropdown__content">
          <a17-colorpicker :color="value" @change="updateValue"></a17-colorpicker>
        </div>
      </a17-dropdown>
    </div>
  </a17-inputframe>
</template>

<script>
  import InputMixin from '@/mixins/input'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import ColorPicker from '@/components/ColorPicker'

  export default {
    name: 'a17ColorField',
    mixins: [InputMixin, InputframeMixin, FormStoreMixin],
    props: {
      name: {
        type: String,
        required: true
      },
      initialValue: {
        default: ''
      }
    },
    components: {
      'a17-colorpicker': ColorPicker
    },
    data: function () {
      return {
        focused: false,
        value: this.initialValue
      }
    },
    computed: {
      bcgStyle: function () {
        return {
          'background-color': this.value !== '' ? this.value : 'transparent'
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
      updateValue: function (newValue) {
        this.value = newValue

        // see formStore mixin
        this.saveIntoStore()
      },
      onBlur: function (event) {
        const newValue = event.target.value
        this.updateValue(newValue)
      },
      onFocus: function () {
        this.focused = true
      },
      onInput: function () {
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
    overflow: visible;

    input {
      padding: 0;
    }
  }

  .form__field--color {

  }

  .form__field--colorBtn {
    cursor:pointer;
    display: block;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    transition: background 250ms;
    box-shadow: 0 0 6px rgba(0, 0, 0, .5);
  }
</style>
