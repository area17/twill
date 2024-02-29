export default {
  props: {
    name: {
      type: String,
      required: true
    },
    isOpen: {
      type: Boolean,
      default: false
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
      return this.nestedEditorName(id)
    },
    nestedEditorName: function (id) {
      return this.name.replace('[', '-').replace(']', '') + '|' + id // nameOfBlock-UniqID|name
    }
  }
}
