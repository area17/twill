export default {
  props: {
    name: {
       type: String,
       required: true
    }
  },
  methods: {
    fieldName: function (id) {
      return this.name + '[' + id + ']'
    }
  }
}
