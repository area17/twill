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
      // Do not use for now : Options newly created will not appear under the options
      if (!this.inModal) return this.options

      const moreOptions = this.optionsByName(this.name)
      const currentOptions = this.options

      // Make sure there is no duplicates
      if (Array.isArray(moreOptions)) {
        moreOptions.forEach(function (option) {
          const currentOptionIndex = currentOptions.findIndex(currentOption => currentOption.value === option.value)
          if (currentOptionIndex === -1) {
            currentOptions.push(option)
          }
        })
      }

      if (moreOptions.length) return currentOptions
      else return this.options
    },
    ...mapGetters([
      'optionsByName'
    ])
  }
}
