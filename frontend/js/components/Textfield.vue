<template>
  <a17-inputframe :error="error" :note="note" :label="label" :locale="locale" @localize="updateLocale" :size="size" :name="name" :label-for="uniqId" :required="required">
    <div class="input__field" :class="textfieldClasses" :dir="dirLocale">
      <span class="input__prefix" v-if="hasPrefix">{{ prefix }}</span>
      <textarea v-if="type === 'textarea'" ref="clone" :rows="rows" class="input__clone" disabled="true" v-model="value"></textarea>
      <textarea v-if="type === 'textarea'"
        ref="input"
        :name="name"
        :id="uniqId"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :readonly="readonly"
        :rows="rows"
        :autofocus="autofocus"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
        v-model="value"
      ></textarea>
      <input v-if="type === 'number'"
        ref="input"
        type="number"
        :placeholder="placeholder"
        :name="name"
        :id="uniqId"
        :disabled="disabled"
        :maxlength="displayedMaxlength"
        :required="required"
        :readonly="readonly"
        :autofocus="autofocus"
        :autocomplete="autocomplete"
        :value="value"
        :min="min"
        :max="max"
        :step="step"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
      />
      <input v-if="type === 'text'"
        ref="input"
        type="text"
        :placeholder="placeholder"
        :name="name"
        :id="uniqId"
        :disabled="disabled"
        :maxlength="displayedMaxlength"
        :required="required"
        :readonly="readonly"
        :autofocus="autofocus"
        :autocomplete="autocomplete"
        :value="value"
        :x-mask="mask"
        @focus="onFocus"
        @blur="onBlur"
        @input="onInput"
      />
      <input v-if="type === 'email'"
        ref="input"
        type="email"
        :placeholder="placeholder"
        :name="name"
        :id="uniqId"
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
      <input v-if="type === 'password'"
        ref="input"
        type="password"
        :placeholder="placeholder"
        :name="name"
        :id="uniqId"
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
      <input v-if="type === 'url'"
        ref="input"
        type="url"
        :placeholder="placeholder"
        :name="name"
        :id="uniqId"
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
      <span :class="validityClasses" v-if="type === 'email'"></span>
    </div>
  </a17-inputframe>
</template>

<script>
  import debounce from 'lodash/debounce'

  import FormStoreMixin from '@/mixins/formStore'
  import InputMixin from '@/mixins/input'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'
  import randKeyMixin from '@/mixins/randKey'

  export default {
    name: 'A17Textfield',
    mixins: [randKeyMixin, InputMixin, InputframeMixin, LocaleMixin, FormStoreMixin],
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
      min: {
        type: Number,
        default: null
      },
      max: {
        type: Number,
        default: null
      },
      step: {
        type: Number,
        default: null
      },
      initialValue: {
        default: ''
      },
      mask: {
        type: String,
        default: null
      },
      rows: {
        type: Number,
        default: 5
      }
    },
    computed: {
      uniqId: function (value) {
        return this.name + '-' + this.randKey
      },
      textfieldClasses: function () {
        return {
          'input__field--textarea': this.type === 'textarea',
          'input__field--small': this.size === 'small' && !this.type === 'textarea',
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
          'input__limit--red': this.counter < (this.maxlength * 0.1)
        }
      },
      validityClasses: function () {
        return [
          'input__validity',
          this.isFieldValid === true ? 'input__validity--valid' : '',
          this.isFieldValid === false ? 'input__validity--error' : ''
        ]
      }
    },
    data: function () {
      return {
        value: this.initialValue,
        lastSavedValue: this.initialValue,
        focused: false,
        isFieldValid: null,
        counter: 0
      }
    },
    watch: {
      initialValue: function () {
        this.updateValue(this.initialValue)
      }
    },
    methods: {
      updateFromStore: function (newValue) { // called from the formStore mixin
        if (typeof newValue === 'undefined') newValue = ''

        if (this.value !== newValue) {
          this.updateValue(newValue)
        }
      },
      updateValue: function (newValue) {
        this.value = newValue
        this.updateCounter(newValue)
      },
      updateAndSaveValue: function (newValue) {
        this.updateValue(newValue)
        this.lastSavedValue = this.value
        this.saveIntoStore() // see formStore mixin
      },
      updateCounter: function (newValue) {
        if (this.maxlength > 0) this.counter = this.maxlength - (newValue ? newValue.toString().length : 0)
      },
      onFocus: function (event) {
        this.focused = true

        this.resizeTextarea()

        this.$emit('focus')
      },
      onBlur: function (event) {
        const newValue = event.target.value
        this.updateAndSaveValue(newValue)

        this.focused = false
        this.$emit('blur', newValue)
      },
      onInput: function (event) {
        this.preventSubmit()

        this._onInputInternal(event)
      },
      _onInputInternal: debounce(function (event) {
        const newValue = event.target.value
        this.updateAndSaveValue(newValue)
        this.checkFieldValidity(event.target)

        this.$emit('change', newValue)
        this.$emit('input', newValue)

        this.allowSubmit()
      }, 250),
      resizeTextarea: function () {
        if (this.type !== 'textarea') return

        const clone = this.$refs.clone
        const minH = 15

        if (clone) {
          const h = clone.scrollHeight
          this.$refs.input.style.minHeight = `${h + minH}px`
        }
      },
      checkFieldValidity: function (el) {
        // Switch based on the type of the field.
        let pattern = null
        let re = null

        switch (el.type) {
          case 'email':
            // If user didn't type any character, return.
            if (el.value.length < 1) {
              this.isFieldValid = null
              return
            }

            pattern = el.pattern
            re = RegExp(pattern)

            // Get pattern and test validity with regex.
            this.isFieldValid = re.test(this.value)
            break
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
    input[type="password"],
    input[type="url"] {
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
      color:inherit;

      @include placeholder() {
        color:$color__f--placeholder;
      }
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
    white-space: nowrap;
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

  .input__validity {
    position: absolute;
    top: 17px;
    right: 15px;
    width: 10px;
    height: 10px;
    background-color: $color__tag--disabled;
    border-radius: 50%;
    user-select: none;
    pointer-events: none;

    &--valid {
      background-color: $color__ok;
    }

    &--error {
      background-color: $color__error;
    }
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
    input[type="password"],
    input[type="url"] {
      height:$height_input - 10px - 2px;
      line-height:$height_input - 10px - 2px;
    }
  }

  /* RTL Direction */
  .input__field--textarea[dir='rtl'] .input__limit {
    left:15px;
    right:auto;
  }
</style>
