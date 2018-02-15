<template>
  <a17-inputframe :name="name" :error="error" :note="note" :label="label" :label-for="uniqId" class="datePicker" :class="{ 'datePicker--static' : static }" :required="required">
    <div class="datePicker__group" :ref="refs.flatPicker">
      <div class="form__field datePicker__field">
        <input type="text" :name="name" :id="uniqId" :placeholder="placeHolder" data-input @blur="onBlur" v-model="date">
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
      static: { // Set static when the input need to show inside a sticky element (in the publish module for example)
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
        flatPicker: null,
        refs: {
          flatPicker: 'flatPicker'
        }
      }
    },
    computed: {
      uniqId: function (value) {
        return this.name + '-' + this.randKey
      },
      config: function () {
        let self = this
        return {
          wrap: true,
          altInput: true,
          static: self.static,
          appendTo: self.static ? self.$refs[self.refs.flatPicker] : null,
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
            self.$emit('input', self.date)
            self.$emit('close', self.date)

            // see formStore mixin
            self.saveIntoStore()
          }
        }
      }
    },
    methods: {
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
      let opts = self.config
      self.flatPicker = new FlatPickr(el, opts)
    },
    beforeDestroy: function () {
      let self = this
      self.flatPicker.destroy()
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .datePicker {
  }

  .datePicker__group {
  }

  .datePicker__field {
    display: flex;
  }

  .datePicker__reset {
    display:block;
    width: 45px - 13px - 14px;
    height: 45px - 13px - 14px;
    overflow:hidden;
    color: $color__background;
    background:$color__icons;
    border-radius:50%;
    margin-top:13px;
    margin-right:13px;
    line-height:45px - 13px - 13px;
    text-align:center;
    transition: opacity 0.2ms ease;

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

  .datePicker--static {
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
