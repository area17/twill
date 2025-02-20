export default {
  props: {
    col: {
      type: Object,
      default: () => { }
    },
    row: {
      type: Object,
      default: () => { }
    },
    editUrl: {
      type: String,
      default: '#'
    },
    editInModal: {
      type: Boolean,
      default: false
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
      this.$emit('editInPlace', { event, lang })
    },
    restoreRow: function () {
      this.$emit('restoreRow', this.row)
    },
    destroyRow: function () {
      this.$emit('destroyRow', this.row)
    },
    deleteRow: function () {
      this.$emit('deleteRow', this.row)
    },
    duplicateRow: function () {
      this.$emit('duplicateRow', this.row)
    }
  }
}
