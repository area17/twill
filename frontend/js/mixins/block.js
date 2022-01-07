export default {
  props: {
    name: {
      type: String,
      required: true
    },
    isOpen: {
      type: Boolean,
      default: false
    },
    errorKey: {
      type: String,
      default: null
    }
  },
  data: function () {
    return {
      opened: this.isOpen
    }
  },
  methods: {
    open: function () {
      this.opened = true
    },
    fieldName: function (id) {
      return this.name + '[' + id + ']' // output : nameOfBlock[UniqID][name]
    },
    repeaterName: function (id) {
      return this.name.replace('[', '-').replace(']', '') + '_' + id // nameOfBlock-UniqID_name
    }
  }
}
