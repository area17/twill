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
      default: function () { return [] }
    },
    options: {
      default: function () { return [] }
    }
  },
  data: function () {
    return {
      currentValue: this.selected
    }
  },
  methods: {
    formatValue: function (newVal, oldval) {
      const isMax = (newVal.length > this.max && this.max > 0)
      const isMin = (newVal.length < this.min && this.min > 0)

      if (isMax || isMin) {
        this.$nextTick(function () {
          this.currentValue = oldval
          this.$emit('change', this.currentValue)
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
