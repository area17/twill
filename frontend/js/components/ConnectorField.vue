<template>
  <div>
    <template v-if="!keepAlive">
      <div v-if="open" ref="fieldContainer">
        <slot></slot>
      </div>
    </template>
    <template v-else>
      <div v-show="open">
        <slot></slot>
      </div>
    </template>
  </div>
</template>

<script>
  import clone from 'lodash/clone'
  import isEqual from 'lodash/isEqual'
  import { mapGetters,mapState } from 'vuex'

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
      },
      keepAlive: {
        type: Boolean,
        default: false
      },
      arrayContains: {
        type: Boolean,
        default: true
      },
      isValueEqual: { // requiredFieldValues must be equal (or different) to the stored value to show
        type: Boolean,
        default: true
      },
      isBrowser: {
        type: Boolean,
        default: false
      },
      matchEmptyBrowser: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      storedValue: function () {
        if (this.inModal) return this.modalFieldValueByName(this.fieldName)
        if (this.isBrowser) return this.selectedBrowser[this.fieldName]
        return this.fieldValueByName(this.fieldName)
      },
      ...mapGetters([
        'fieldValueByName',
        'modalFieldValueByName'
      ]),
      ...mapState({
        fields: state => state.form.fields, // Fields in the form
        modalFields: state => state.form.modalFields, // Fields in the create/edit modal
        selectedBrowser: state => state.browser.selected
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

        if (this.$refs.fieldContainer) {
          this.$slots.default.forEach((child) => {
            // Base input fields.
            if (
              child.componentInstance !== undefined &&
              child.componentInstance.$refs &&
              child.componentInstance.$refs.field
            ) {
              if (child.componentInstance.$refs.field[0]) {
                child.componentInstance.$refs.field[0].destroyValue()
              }
            }
            // Special fields such as browsers.
            else if (
              child.componentInstance !== undefined &&
              child.componentInstance.$slots !== undefined &&
              child.componentInstance.$slots.default !== undefined
            ) {
              child.componentInstance.$slots.default.forEach((subChild) => {
                if (subChild.componentInstance && subChild.componentInstance.destroyValue) {
                  subChild.componentInstance.destroyValue()
                }
              })
            } else if (
              child.componentInstance.destroyValue
            ) {
              child.componentInstance.destroyValue()
            }
          })
        }

        if (this.isBrowser) {
          const browserLength = (value && value.length) ?? 0
          if (this.matchEmptyBrowser && (browserLength === 0)) {
            this.open = true
            return
          }

          this.open = this.matchEmptyBrowser ? false : browserLength > 0
          return
        }

        const newValue = clone(value)
        const newFieldValues = clone(this.requiredFieldValues)
        const newFieldValuesArray = Array.isArray(newFieldValues) ? newFieldValues : [newFieldValues]

        // sort requiredFieldValues and value if is array, so the order of values is the same
        if (Array.isArray(newFieldValues)) newFieldValues.sort()
        if (Array.isArray(newValue)) newValue.sort()

        // update visiblity
        if (this.isValueEqual) {
          if (Array.isArray(newValue)) {
            this.open = this.arrayContains ? newFieldValuesArray.some((value) => {
              return newValue.includes(value)
            }) : this.open = JSON.stringify(newFieldValuesArray) === JSON.stringify(newValue)
          } else {
            this.open = (Array.isArray(newFieldValues)) ? newFieldValues.indexOf(newValue) !== -1 : isEqual(newValue, newFieldValues)
          }
        } else {
          if (Array.isArray(newValue)) {
            this.open = this.arrayContains ? newFieldValuesArray.every((value) => {
              return !newValue.includes(value)
            }) : this.open = JSON.stringify(newFieldValuesArray) !== JSON.stringify(newValue)
          } else {
            this.open = (Array.isArray(newFieldValues)) ? newFieldValues.indexOf(newValue) === -1 : !isEqual(newValue, newFieldValues)
          }
        }
      }
    },
    mounted: function () {
      const self = this
      // init show/hide
      this.$nextTick(function () {
        self.toggleVisibility(this.storedValue)
      })
    }
  }
</script>
