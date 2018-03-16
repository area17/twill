export default {
  props: {
    maxlength: {
      type: Number,
      default: 0
    }
  },
  data: function () {
    return {
      value: this.initialValue,
      counter: 0
    }
  },
  computed: {
    hasMaxlength: function () {
      return this.maxlength > 0
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
  methods: {
    onClickLocale: function () {
      this.$emit('localize', this.locale)
    },
    updateCounter: function (newValue) {
      if (this.maxlength > 0) this.counter = this.maxlength - newValue.toString().length
    }
  }
}
