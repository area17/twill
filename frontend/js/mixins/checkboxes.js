import { mapState } from 'vuex'
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
  watch: {
    selected: function (value) {
      this.currentValue = value
    }
  },
  computed: {
    fullOptions: function () {
      if(this.moreOptions)
      return this.options.concat(this.moreOptions)
    },
    fullOptions: function () {
      const moreOptions = this.optionsByName(this.name)

      if(moreOptions.length) return this.options.concat(moreOptions)
      else return this.options
    },
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
    },
    ...mapGetters([
      'optionsByName'
    ])
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
