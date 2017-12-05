import { mapState } from 'vuex'

export default {
  props: {
    inStore: {
      default: ''
    },
    fieldName: {
      default: ''
    }
  },
  computed: {
    ...mapState({
      submitting: state => state.form.loading,
      fields: state => state.form.fields
    })
  },
  data: function () {
    return {
      _originalValue: ''
    }
  },
  methods: {
    getFieldName: function () {
      return this.fieldName !== '' ? this.fieldName : this.name
    },
    saveIntoStore: function (value) {
      if (this.inStore === '') return

      let newValue = ''

      if (value) newValue = value
      else newValue = this[this.inStore]

      // There is no change on the field here
      if (this[this._originalValue] === newValue) return

      let field = {}
      field.name = this.getFieldName()
      field.value = newValue
      if (this.locale) field.locale = this.locale.value
      this.$store.commit('updateFormField', field)
    }
  },
  beforeMount: function () {
    const fieldName = this.getFieldName()

    if (this.inStore === '') return
    if (fieldName === '') return

    const fieldInStore = this.fields.filter(function (field) {
      return field.name === fieldName
    })

    // init value with the one from the store
    if (fieldInStore.length) {
      if (this.locale) {
        this[this.inStore] = fieldInStore[0].value[this.locale.value]
      } else {
        this[this.inStore] = fieldInStore[0].value
      }
      this[this._originalValue] = this[this.inStore]
    }
  },
  beforeDestroy: function () {
    if (this.inStore !== '') {
      // Delete form field from store because the field has been removed
      this.$store.commit('removeFormField', this.getFieldName())
    }
  }
}
