export default {
  props: {
    name: {
      type: String,
      required: true
    }
  },
  methods: {
    fieldName: function (id) {
      return this.name + '[' + id + ']' // output : nameOfBlock[UniqID][name]
    },
    repeaterName: function (id) {
      return this.name.replace('[', '-').replace(']', '') + '_' + id // nameOfBlock-UniqID_name
    }
  }
}
