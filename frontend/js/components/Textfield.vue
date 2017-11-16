<template>
  <a17-inputframe :error="error" :note="note" :label="label" :locale="locale" @localize="updateLocale" :size="size" :name="name">
    <div class="input__field" :class="textfieldClasses">
      <span class="input__prefix" v-if="hasPrefix">{{ prefix }}</span>
      <textarea v-if="type === 'textarea'" ref="clone" :rows="rows" class="input__clone" disabled="true">{{ value }}</textarea>
      <textarea v-if="type === 'textarea'"
        ref="input"
        :name="name"
        :id="name"
        :disabled="disabled"
        :required="required"
        :readonly="readonly"
        :rows="rows"
        :autofocus="autofocus"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
      >{{ value }}</textarea>
      <input v-if="type == 'number'"
        ref="input"
        type="number"
        :placeholder="placeholder"
        :name="name"
        :id="name"
        :disabled="disabled"
        :maxlength="displayedMaxlength"
        :required="required"
        :readonly="readonly"
        :autofocus="autofocus"
        :autocomplete="autocomplete"
        :value="value"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
      />
      <input v-if="type == 'text'"
        ref="input"
        type="text"
        :placeholder="placeholder"
        :name="name"
        :id="name"
        :disabled="disabled"
        :maxlength="displayedMaxlength"
        :required="required"
        :readonly="readonly"
        :autofocus="autofocus"
        :autocomplete="autocomplete"
        :value="value"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
      />
      <input v-if="type == 'email'"
        ref="input"
        type="email"
        :placeholder="placeholder"
        :name="name"
        :id="name"
        :disabled="disabled"
        :maxlength="displayedMaxlength"
        :required="required"
        :readonly="readonly"
        :autofocus="autofocus"
        :autocomplete="autocomplete"
        pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
        :value="value"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
      />
      <input v-if="type == 'password'"
        ref="input"
        type="password"
        :placeholder="placeholder"
        :name="name"
        :id="name"
        :disabled="disabled"
        :maxlength="displayedMaxlength"
        :required="required"
        :readonly="readonly"
        :autofocus="autofocus"
        :autocomplete="autocomplete"
        :value="value"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
      />
      <span class="input__limit f--tiny" :class="limitClasses" v-if="hasMaxlength">{{ counter }}</span>
    </div>
  </a17-inputframe>
</template>

<script>
  import InputMixin from '@/mixins/input'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'

  export default {
    name: 'A17Textfield',
    mixins: [InputMixin, InputframeMixin, LocaleMixin, FormStoreMixin],
    props: {
      name: {
        type: String,
        required: true
      },
      type: {
        type: String,
        default: 'text'
      },
      prefix: {
        type: String,
        default: ''
      },
      maxlength: {
        type: Number,
        default: 0
      },
      initialValue: {
        default: ''
      },
      rows: {
        type: Number,
        default: 5
      }
    },
    computed: {
      textfieldClasses: function () {
        return {
          'input__field--textarea': this.type === 'textarea',
          'input__field--small': this.size === 'small',
          's--focus': this.focused,
          's--disabled': this.disabled
        }
      },
      hasMaxlength: function () {
        return this.maxlength > 0
      },
      hasPrefix: function () {
        return this.prefix !== ''
      },
      displayedMaxlength: function () {
        if (this.hasMaxlength) return this.maxlength
        else return false
      },
      limitClasses: function () {
        return {
          'input__limit--red': this.counter < 10
        }
      }
    },
    data: function () {
      return {
        value: this.initialValue,
        focused: false,
        counter: 0
      }
    },
    watch: {
      initialValue: function () {
        this.updateValue(this.initialValue)
      }
    },
    methods: {
      updateValue: function (newValue) {
        this.value = newValue
        this.updateCounter(newValue)
      },
      updateCounter: function (newValue) {
        if (this.maxlength > 0) this.counter = this.maxlength - newValue.toString().length
      },
      onFocus: function (event) {
        this.focused = true

        this.$emit('focus')
      },
      onBlur: function (event) {
        let newValue = event.target.value

        this.focused = false

        // see formStore mixin
        this.saveIntoStore()

        this.$emit('blur', newValue)
      },
      onInput: function (event) {
        let newValue = event.target.value

        this.updateValue(newValue)
        this.$emit('change', newValue)
      },
      resizeTextarea: function () {
        const clone = this.$refs.clone

        if (clone) {
          let h = clone.scrollHeight
          this.$refs.input.style.minHeight = `${h + 15}px`
        }
      }
    },
    mounted: function () {
      this.updateCounter(this.value)

      if (this.type === 'textarea') {
        this.resizeTextarea()
        this.$watch('value', this.resizeTextarea)

        this.$nextTick(function () {
          window.addEventListener('resize', this.resizeTextarea)
        })
      }
    },
    beforeDestroy () {
      if (this.type === 'textarea') window.removeEventListener('resize', this.resizeTextarea)
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  $height_input: 45px;

  .input__field {
    position:relative;
    overflow: hidden;
    padding:0 15px;

    height:$height_input;
    line-height:$height_input;
    @include textfield;
    @include defaultState;
    display: flex;
    flex-direction: row;
    flex-wrap:no-wrap;

    &.s--focus {
      @include focusState;
    }

    &:hover {
      @include focusState;
    }

    &.s--disabled {
      @include disabledState;
    }

    input[type="search"],
    input[type="number"],
    input[type="text"],
    input[type="email"],
    input[type="password"] {
      @include resetfield;
      height:$height_input - 2px;
      line-height:$height_input - 2px;
      flex-grow: 1;
      color:inherit;

      @include placeholder() {
        color:$color__f--placeholder;
      }
    }

    textarea {
      @include resetfield;
      padding:10px;
      line-height:inherit;
      width:100%;
      box-sizing: border-box;
      display:block;
      resize: none;
      overflow: hidden;
      z-index:1;
      position:relative;
    }

    .input__clone {
      position:absolute;
      width:100%;
      pointer-events:none;
      opacity:0;
      height: auto;
      z-index:0;
    }
  }

  .input__prefix {
    height:$height_input - 2px;
    line-height:$height_input - 2px;
    user-select:none;
    color:$color__icons;
    pointer-events: none;
  }

  .input__limit {
    height:$height_input - 2px;
    line-height:$height_input - 2px;
    color:$color__text--light;
    user-select: none;
    pointer-events:none;
  }

  .input__limit--red {
    color:red;
  }

  .input__field--textarea {
    display:block;
    padding:0;
    height:auto;
    line-height:inherit;

    .input__prefix {
      display:none;
    }

    .input__limit {
      position:absolute;
      right:15px;
      bottom:0;
    }
  }

  .input__field--small {
    padding:0 13px;
    height:$height_input - 10px;
    line-height:$height_input - 10px;

    input[type="search"],
    input[type="number"],
    input[type="text"],
    input[type="email"],
    input[type="password"] {
      height:$height_input - 10px;
      line-height:$height_input - 10px;
    }
  }
</style>
