<template>
  <a17-locale type="a17-textfield" :initialValues="initialValues" :attributes="attributes" @change="saveMetadata"></a17-locale>
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
        initialValues: {}
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
      attributes: function () {
        return {
          label: this.label,
          name: `${this.name}[${this.id}]`,
          type: 'text',
          placeholder: this.placeholder,
          inStore: 'value'
        }
      },
      placeholder: function () {
        if (this.defaultMetadatas) {
          return this.defaultMetadatas !== null ? this.defaultMetadatas : ''
        } else {
          return ''
        }
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
    },
    mounted: function () {
      let initialValues = {}
      let index = 0

      this.languages.forEach((lang) => {
        const langVal = lang.value

        if (this.customMetadatas) {
          if (this.customMetadatas[langVal]) {
            initialValues[langVal] = this.customMetadatas[langVal]
          } else if (typeof this.customMetadatas === 'string' && index === 0) {
            initialValues[langVal] = this.customMetadatas
          }
        }

        index++
      })

      this.initialValues = initialValues
    }
  }
</script>
