<script>
  // ExtendedVSelect.vue
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
    watch: {
      search () {
        this.onSearch(this.search, this.toggleLoading)
        this.$emit('search', this.search, this.toggleLoading)
      }
    },
    methods: {
      /**
       * Toggle the visibility of the dropdown menu.
       * @param  {Event} e
       * @return {void}
       */
      toggleDropdown (e) {
        if (!this.disabled) {
          if (e.target === this.$refs.openIndicator ||
            e.target === this.$refs.search ||
            e.target === this.$refs.toggle ||
            e.target === this.$refs.selectedOptions ||
            e.target === this.$el) {
              if (this.open) {
                this.$refs.search.blur() // dropdown will close on blur
              } else {
                this.open = true
                this.$refs.search.focus()
              }
            }
        }
      },
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
      }
    },
    mounted () {
      if (this.taggable) this.onSearch(this.search, this.toggleLoading)
    }
  }
</script>
