export default {
  props: {
    name: {
      type: String,
      required: true
    },
    opened: {
      type: Boolean,
      default: false
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
