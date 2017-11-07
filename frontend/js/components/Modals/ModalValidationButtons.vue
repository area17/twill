<template>
  <a17-inputframe>
    <template v-if="mode === 'create'">
      <a17-button type="submit" name="create" variant="validate" :disabled="isDisabled">Create</a17-button>
      <a17-button type="submit" name="create-another" v-if="!isDisabled" variant="aslink-grey"><span>Create and add another</span></a17-button>
    </template>
    <a17-button type="submit" name="update" v-else="" variant="validate" :disabled="isDisabled">Update</a17-button>
  </a17-inputframe>
</template>

<script>
  export default {
    name: 'A17ModalValidationButtons',
    props: {
      mode: {
        type: String, // create / update
        default: 'create'
      }
    },
    data: function () {
      return {
        fields: false,
        isDisabled: true
      }
    },
    methods: {
      disabled: function () {
        if (!this.fields) {
          this.isDisabled = true
          return
        }

        // There are not required fields, so button are enabled
        if (this.fields.length === 0) {
          this.isDisabled = false
          return
        }

        // If all required items have a value
        const filtered = this.fields.filter(function (field) {
          return field.value.length > 0
        })

        if (filtered.length === this.fields.length) {
          this.isDisabled = false
          return
        }

        this.isDisabled = true
      }
    },
    mounted: function () {
      let self = this

      self.fields = [...this.$parent.$el.querySelectorAll('input[required], textarea[required], select[required]')]

      self.fields.forEach(function (field) {
        field.addEventListener('input', self.disabled)
      })
    },
    beforeDestroy: function () {
      let self = this
      self.fields.forEach(function (field) {
        field.removeEventListener('input', self.disabled)
      })
    }
  }
</script>

<style lang="scss">

</style>
