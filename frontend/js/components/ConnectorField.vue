<template>
  <div v-show="open">
    <slot></slot>
  </div>
</template>

<script>
  import { isEqual, clone } from 'lodash'
  import { mapState, mapGetters } from 'vuex'

  export default {
    name: 'A17ConnectorField',
    props: {
      fieldName: {
        type: String,
        required: true
      },
      requiredFieldValues: {
        default: ''
      },
      inModal: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      storedValue: function () {
        if (this.inModal) return this.modalFieldValueByName(this.fieldName)
        else return this.fieldValueByName(this.fieldName)
      },
      ...mapGetters([
        'fieldValueByName',
        'modalFieldValueByName'
      ]),
      ...mapState({
        fields: state => state.form.fields, // Fields in the form
        modalFields: state => state.form.modalFields // Fields in the create/edit modal
      })
    },
    data: function () {
      return {
        open: false
      }
    },
    watch: {
      storedValue: function (fieldInstore) {
        this.toggleVisibility(fieldInstore)
      }
    },
    methods: {
      toggleVisibility: function (value) {
        let newValue = clone(value)
        let newFieldValues = clone(this.requiredFieldValues)

        // sort requiredFieldValues and value if is array, so the order of values is the same
        if (Array.isArray(newFieldValues)) newFieldValues.sort()
        if (Array.isArray(newValue)) newValue.sort()

        // update visiblity
        this.open = isEqual(newValue, newFieldValues)
      }
    },
    mounted: function () {
      let self = this
      // init show/hide
      this.$nextTick(function () {
        self.toggleVisibility(this.storedValue)
      })
    }
  }
</script>
