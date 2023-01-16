<script>
// ExtendedVSelect.vue
  import 'vue-select/dist/vue-select.css'

  import vSelect from 'vue-select'

  export default {
    extends: vSelect,
    props: {
      /**
       * Enable/Disable deselect the option by double select it
       * @type {Boolean}
       */
      toggleSelectOption: {
        type: Boolean,
        default: false
      },
      /**
       * option to active a not null select
       * @type {Boolean}
       */
      requiredValue: {
        type: Boolean,
        default: false
      },
      /**
       * Enable/Disable
       * @type {Boolean}
       */
      disabled: {
        type: Boolean,
        default: false
      }
    },
    data () {
      return {
        // Set mutableValue with current props value to prevent from first watch and onchange call
        mutableValue: this.value
      }
    },
    computed: {
      showClearButton () {
        return false
      }
    },
    methods: {
      /**
       * Delete the value on Delete keypress when there is no
       * text in the search input, & there's tags to delete
       * @return {this.value}
       */
      maybeDeleteValue () {
        if (!this.requiredValue && !this.$refs.search.value.length && this.mutableValue) {
          // eslint-disable-next-line no-return-assign
          return this.multiple ? this.mutableValue.pop() : this.mutableValue = null
        }
      },
      /**
       * Check if the given option is currently selected.
       * @param  {Object|String}  option
       * @return {Boolean}        True when selected | False otherwise
       * https://github.com/sagalbot/vue-select/commit/8a601c0ac3311adb89bc6e31b8cf215b1343d93c
       */
      isOptionSelected (option) {
        if (this.valueAsArray === undefined) {
          return false;
        }
        return this.valueAsArray.some(value => {
          if (typeof value === 'object') {
            return this.optionObjectComparator(value, option)
          }
          return value === option || value === option[this.index]
        })
      }
    },
    mounted () {
      if (this.taggable) this.$emit('search', this.search, this.toggleLoading)
    }
  }
</script>
