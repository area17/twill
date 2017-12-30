export default {
  props: {
    label: {
      type: String,
      default: ''
    },
    size: {
      type: String,
      default: 'large' // large, small
    },
    note: {
      type: String,
      default: ''
    }
  },

  computed: {
    errorMessage () {
      return 'The field has error'
    },
    error () {
      this.$store.state.form ? this.$store.state.form.errors.includes(this.name) : false
    }
  }
}
