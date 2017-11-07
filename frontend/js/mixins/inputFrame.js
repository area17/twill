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
    },
    error: {
      type: Boolean,
      default: false
    }
  }
}
