<template>
  <a17-inputframe :name="name" :error="error" :note="note" :label="label" :label-for="uniqId" class="datePicker" :class="{ 'datePicker--static' : staticMode, 'datePicker--mobile' : isMobile }" :required="required">
    <div class="datePicker__group" :ref="refs.flatPicker">
      <div class="form__field datePicker__field">
        <input type="text" :name="name" :id="uniqId" :required="required" :placeholder="placeHolder" data-input @blur="onBlur" v-model="date">
        <a href="#" v-if="clear" class="datePicker__reset" :class="{ 'datePicker__reset--cleared' : !date }" @click.prevent="onClear"><span v-svg symbol="close_icon"></span></a>
      </div>
    </div>
  </a17-inputframe>
</template>

<script>
  import randKeyMixin from '@/mixins/randKey'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import FlatPickr from 'flatpickr'
  import 'flatpickr/dist/flatpickr.css'

  export default {
    name: 'A17DatePicker',
    mixins: [randKeyMixin, InputframeMixin, FormStoreMixin],
    props: {
      /* @see: https://chmln.github.io/flatpickr/options/ */
      name: { // FlatPicker hidden input name
        type: String,
        default: 'date'
      },
      required: {
        type: Boolean,
        default: false
      },
      placeHolder: {
        type: String,
        default: ''
      },
      allowInput: {
        type: Boolean,
        default: false
      },
      enableTime: {
        type: Boolean,
        default: false
      },
      noCalendar: {
        type: Boolean,
        default: false
      },
      time_24hr: {
        type: Boolean,
        default: false
      },
      inline: {
        type: Boolean,
        default: false
      },
      initialValue: {
        type: String,
        default: null
      },
      hourIncrement: {
        type: Number,
        default: 1
      },
      minuteIncrement: {
        type: Number,
        default: 30
      },
      staticMode: { // Set static when the input need to show inside a sticky element (in the publish module for example)
        type: Boolean,
        default: false
      },
      minDate: {
        type: String,
        default: null
      },
      maxDate: {
        type: String,
        default: null
      },
      mode: {
        type: String,
        default: 'single',
        validator: function (value) {
          return value === 'single' || value === 'multiple' || value === 'range'
        }
      },
      clear: {
        type: Boolean,
        default: false
      }
    },
    data: function () {
      return {
        date: this.initialValue,
        isMobile: false,
        flatPicker: null,
        refs: {
          flatPicker: 'flatPicker'
        }
      }
    },
    computed: {
      uniqId: function (value) {
        return this.name + '-' + this.randKey
      }
    },
    methods: {
      config: function () {
        let self = this
        return {
          wrap: true,
          altInput: true,
          static: self.staticMode,
          appendTo: self.staticMode ? self.$refs[self.refs.flatPicker] : undefined,
          enableTime: self.enableTime,
          noCalendar: self.noCalendar,
          time_24hr: self.time_24hr,
          inline: self.inline,
          allowInput: self.allowInput,
          mode: self.mode,
          minuteIncrement: self.minuteIncrement,
          hourIncrement: self.hourIncrement,
          minDate: self.minDate,
          maxDate: self.maxDate,
          onOpen: function () {
            setTimeout(function () {
              self.flatPicker.set('maxDate', self.maxDate) // in case maxDate changed since last open
              self.flatPicker.set('minDate', self.minDate) // in case minDate changed since last open
            }, 10)

            self.$emit('open', self.date)
          },
          onClose: function (selectedDates, dateStr, instance) {
            self.$nextTick(function () { // wait for the datepicker to properly update the UI
              self.$emit('input', self.date)
              self.$emit('close', self.date)

              // see formStore mixin
              self.saveIntoStore()
            })
          }
        }
      },
      updateFromStore: function (newValue) { // called from the formStore mixin
        if (newValue !== this.date) {
          this.date = newValue
          this.flatPicker.setDate(newValue)
        }
      },
      onInput: function (evt) {
        this.$emit('input', this.date)
      },
      onBlur: function () {
        this.$emit('blur', this.date)
      },
      onClear: function () {
        this.flatPicker.clear()

        // see formStore mixin
        this.saveIntoStore()

        this.$emit('input', this.date)
      }
    },
    mounted: function () {
      let self = this
      let el = self.$refs[self.refs.flatPicker]
      let opts = self.config()
      self.flatPicker = new FlatPickr(el, opts)

      this.isMobile = self.flatPicker.isMobile
    },
    beforeDestroy: function () {
      let self = this
      self.flatPicker.destroy()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .datePicker__field {
    display: flex;
  }

  .datePicker__reset {
    $button-reset__width:45px - 13px - 14px;
    display:block;
    width: $button-reset__width;
    flex: 0 0 $button-reset__width;
    height: $button-reset__width;
    overflow:hidden;
    color: $color__background;
    background:$color__icons;
    border-radius:#{$button-reset__width / 2};
    margin-top:13px;
    margin-right:13px;
    line-height:$button-reset__width;
    text-align:center;
    transition: opacity 0.2s ease;

    .icon {
      overflow: hidden;
      vertical-align: top;
      position: relative;
      top: 4px;
    }

    &:hover,
    &:focus {
      background:$color__fborder--active;
    }
  }

  .datePicker__reset.datePicker__reset--cleared {
    opacity:0;
    pointer-events:none;
  }

  /* Static variant (but not in the mobile version) */
  .datePicker--static:not(.datePicker--mobile) {
    .form__field {
      height:0;
      position:static;
      overflow:visible;
      border:0 none;
    }
    .datePicker__reset {
      position:absolute;
      right:0;
      top:0;
    }
  }

  .flatpickr-wrapper {
    display:block;
  }
</style>

<style lang="scss">
  /* Mobile version */
  .datePicker__group input.flatpickr-input.flatpickr-mobile {
    width: 100%;
    font-family: inherit;
    font-size: inherit;
    background: transparent;
    border: 0 none;
    padding: 0 15px;
    -webkit-appearance: none;

    &::-webkit-clear-button {
      display: none;
    }

    &::-webkit-inner-spin-button {
      display: none;
    }

    &::-webkit-calendar-picker-indicator {
      display: none;
    }
  }
</style>
