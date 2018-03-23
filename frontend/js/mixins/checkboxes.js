import isEqual from 'lodash/isEqual'

export default {
  props: {
    min: {
      type: Number,
      default: 0
    },
    max: {
      type: Number,
      default: 0
    },
    disabled: {
      type: Boolean,
      default: false
    },
    selected: {
      type: Array,
      default: function () { return [] }
    }
  },
  data: function () {
    return {
      currentValue: this.selected
    }
  },
  watch: {
    selected: function (value) {
      this.currentValue = value
    }
  },
  computed: {
    checkedValue: {
      get: function () {
        return this.currentValue
      },
      set: function (value) {
        if (!isEqual(value, this.currentValue)) {
          this.currentValue = value
          if (typeof this.saveIntoStore !== 'undefined') this.saveIntoStore(value)
          this.$emit('change', value)
        }
      }
    }
  },
  methods: {
    isMax: function (arrayToTest) {
      return (arrayToTest.length > this.max && this.max > 0)
    },
    isMin: function (arrayToTest) {
      return (arrayToTest.length < this.min && this.min > 0)
    }
  }
}
