import { isEqual } from 'lodash'

export default {
  props: {
    name: {
      type: String,
      default: ''
    },
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
    },
    options: {
      type: Array,
      default: function () { return [] }
    }
  },
  data: function () {
    return {
      currentValue: this.selected
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
    formatValue: function (newVal, oldval) {
      if (!newVal) return
      if (!oldval) return

      const isMax = (newVal.length > this.max && this.max > 0)
      const isMin = (newVal.length < this.min && this.min > 0)

      if (isMax || isMin) {
        this.$nextTick(function () {
          this.checkedValue = oldval
        })
      }
    }
  },
  mounted: function () {
    if ((this.max + this.min) > 0) {
      this.$watch('currentValue', this.formatValue, {
        immediate: true
      })
    }
  }
}
