<template>
  <a17-locale type="a17-textfield" :attributes="attributes" :initialValues="initialValues" :initialValue="initialValue" @change="saveMetadata"></a17-locale>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    name: 'A17MediaMetadata',
    props: {
      media: {
        type: Object,
        default: () => {}
      },
      name: {
        type: String,
        required: true
      },
      id: {
        type: String,
        required: true
      },
      label: {
        type: String,
        required: true
      }
    },
    data: function () {
      return {
      }
    },
    computed: {
      defaultMetadatas: function () {
        if (this.media.hasOwnProperty('metadatas')) {
          return this.media.metadatas.default[this.id] || false
        } else {
          return false
        }
      },
      customMetadatas: function () {
        if (this.media.hasOwnProperty('metadatas')) {
          return this.media.metadatas.custom[this.id] || false
        } else {
          return false
        }
      },
      hasLanguages: function () {
        return this.languages.length > 1
      },
      attributes: function () {
        let self = this
        return {
          label: self.label,
          name: `${self.name}_${self.id}`,
          type: 'text',
          placeholder: self.placeholder
        }
      },
      placeholder: function () {
        if (this.defaultMetadatas) {
          return this.defaultMetadatas !== null ? this.defaultMetadatas : ''
        } else {
          return ''
        }
      },
      initialValue: function () {
        let self = this
        let initialValue = ''

        // Do we have only one language
        if (!this.hasLanguages) {
          this.languages.forEach(function (lang) {
            // Custom or default values
            if (self.customMetadatas) {
              initialValue = self.customMetadatas !== null ? self.customMetadatas : ''
            } else if (self.defaultMetadatas) {
              initialValue = self.defaultMetadatas !== null ? self.defaultMetadatas : ''
            }
          })
        }

        return initialValue
      },
      initialValues: function () {
        let self = this
        let initialValues = {}

        // Do we have many languages
        if (this.hasLanguages) {
          this.languages.forEach(function (lang) {
            const langVal = lang.value

            // Custom or default values
            if (self.customMetadatas) {
              initialValues[langVal] = self.customMetadatas[langVal] !== null ? self.customMetadatas[langVal] : ''
            } else if (self.defaultMetadatas) {
              initialValues[langVal] = self.defaultMetadatas
            }
          })
        }

        return initialValues
      },
      ...mapState({
        languages: state => state.language.all
      })
    },
    methods: {
      saveMetadata: function (newDatas) {
        newDatas.id = this.id
        this.$emit('change', newDatas)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';
</style>
