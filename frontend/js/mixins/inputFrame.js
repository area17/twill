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
    error () {
      return this.$store.state.form ? this.$store.state.form.errors.includes(this.name) : false
    }
  }
}
