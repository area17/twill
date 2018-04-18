export default {
  props: {
    col: {
      type: Object,
      required: true
    },
    row: {
      type: Object,
      required: true
    },
    editUrl: {
      type: String,
      required: true
    },
    editInModal: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    colName: function () {
      return this.col.hasOwnProperty('name') ? this.col.name : ''
    }
  },
  methods: {
    update: function () {
      this.$emit('update', { row: this.row, col: this.colName })
    },
    preventEditInPlace: function (event) {
      if (this.editInModal) {
        event.preventDefault()
      }

      this.editInPlace()
    },
    editInPlace: function (event, lang) {
      this.$emit('editInPlace', {event: event, lang: lang})
    },
    restoreRow: function () {
      this.$emit('restoreRow', this.row)
    },
    deleteRow: function () {
      this.$emit('deleteRow', this.row)
    }
  }
}
