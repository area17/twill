export default {
  props: {
    autofocus: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    placeholder: {
      type: String,
      default: ''
    },
    direction: {
      type: String,
      default: 'auto'
    },
    name: {
      default: ''
    },
    readonly: {
      type: Boolean,
      default: false
    },
    required: {
      type: Boolean,
      default: false
    },
    autocomplete: {
      type: String,
      default: 'on'
    }
  }
}
