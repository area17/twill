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
    errorKey () {
      return this.hasLocale ? (this.name.replace('[', '.').replace(']', '')) : this.name
    },
    errorMessage () {
      let message = this.error ? this.$store.state.form.errors[this.errorKey][0] : ''
      return message.endsWith('is required.') ? '' : message
    },
    error () {
      return this.$store.state.form ? Object.keys(this.$store.state.form.errors).includes(this.errorKey) : false
    }
  }
}
