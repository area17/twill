import { mapGetters } from 'vuex'

export default {
  props: {
    name: {
      type: String,
      default: ''
    },
    addNew: {
      type: String,
      default: ''
    },
    options: {
      type: Array,
      default: function () { return [] }
    }
  },
  computed: {
    fullOptions: function () {
      const moreOptions = this.optionsByName(this.name)
      const currentOptions = this.options

      // Make sure there is no duplicates in the options
      if (Array.isArray(moreOptions)) {
        moreOptions.forEach(function (option) {
          const currentOptionIndex = currentOptions.findIndex(currentOption => currentOption.value === option.value)
          if (currentOptionIndex === -1) {
            currentOptions.push(option)
          }
        })
      }

      // return options or options + newly created options available in the store
      if (moreOptions.length) return currentOptions
      else return this.options
    },
    ...mapGetters([
      'optionsByName'
    ])
  }
}
