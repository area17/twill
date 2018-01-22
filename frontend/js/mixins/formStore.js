import { mapState, mapGetters } from 'vuex'

export default {
  props: {
    hasDefaultStore: {
      type: Boolean,
      default: false
    },
    inStore: {
      type: String,
      default: ''
    },
    fieldName: {
      type: String,
      default: ''
    }
  },
  computed: {
    storedValue: function () {
      return this.fieldValueByName(this.getFieldName())
    },
    ...mapGetters([
      'fieldValueByName'
    ]),
    ...mapState({
      submitting: state => state.form.loading,
      fields: state => state.form.fields
    })
  },
  watch: {
    storedValue: function (fieldInstore) {
      if (this.inStore === '') return

      const currentValue = this[this.inStore]
      const newValue = (this.locale) ? fieldInstore[this.locale.value] : fieldInstore

      // new value detected, let's update the UI (updateFromStore method need to be present into the component so the value is properly updated)
      if (currentValue !== newValue) {
        if (typeof this.updateFromStore !== 'undefined') this.updateFromStore(newValue)
      }
    }
  },
  methods: {
    getFieldName: function () {
      return this.fieldName !== '' ? this.fieldName : this.name
    },
    // Save the value into the store
    saveIntoStore: function (value) {
      if (this.inStore === '') return

      let newValue = ''

      if (value) newValue = value
      else newValue = this[this.inStore]

      // The object that is saved
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

    if (fieldInStore.length) {
      // init value with the one from the store

      if (this.locale) {
        this[this.inStore] = fieldInStore[0].value[this.locale.value]
      } else {
        this[this.inStore] = fieldInStore[0].value
      }
    } else if (this.hasDefaultStore) {
      // init value with the one present into the component itself
      // used for select / single-selects
      this.saveIntoStore()
    }
  },
  beforeDestroy: function () {
    if (this.inStore !== '') {
      // Delete form field from store because the field has been removed
      this.$store.commit('removeFormField', this.getFieldName())
    }
  }
}
