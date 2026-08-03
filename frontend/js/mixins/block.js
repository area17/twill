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
    fieldName: function (id, extra = null) {
      const fieldName = this.name + '[' + id + ']' // output : nameOfBlock[UniqID][name]
      return extra ? fieldName + extra : fieldName
    },
    repeaterName: function (id) {
      return this.name.replace('[', '-').replace(']', '') + '|' + id // nameOfBlock-UniqID|name
    },
    nestedEditorName: function (id) {
      return this.name.replace('[', '-').replace(']', '') + '|' + id // nameOfBlock-UniqID|name
    }
  }
}
