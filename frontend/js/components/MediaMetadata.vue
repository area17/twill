<template>
  <a17-locale v-if="languages.length > 1 && fieldType === 'text'" type="a17-textfield" :initialValues="initialValues" :attributes="attributes" @change="saveMetadata"></a17-locale>
  <a17-textfield v-else-if="fieldType === 'text'" :label="label" :name="fieldName" type="text" :placeholder="placeholder" :initialValue="initialValue" in-store="value" @change="saveMetadata"></a17-textfield>
  <div class="mediaMetadata__checkbox" v-else-if="fieldType === 'checkbox'" >
    <a17-checkbox :label="label" :name="fieldName" :initialValue="initialValue" :value="1" @change="saveMetadata" inStore="value" />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { FORM } from '@/store/mutations'

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
      },
      type: {
        type: String,
        required: false
      }
    },
    data: function () {
      return {
        initialValues: {},
        initialValue: ''
      }
    },
    computed: {
      fieldName: function () {
        return `${this.name}[${this.id}]`
      },
      fieldType: function () {
        return this.type ? this.type : 'text'
      },
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
          name: this.fieldName,
          type: 'text',
          placeholder: this.placeholder,
          inStore: 'value'
        }
      },
      placeholder: function () {
        if (this.defaultMetadatas) {
          if (typeof this.defaultMetadatas === 'object') {
            return this.defaultMetadatas.hasOwnProperty(this.currentLocale) ? this.defaultMetadatas[this.currentLocale] : ''
          } else {
            return this.defaultMetadatas !== null ? this.defaultMetadatas : ''
          }
        } else {
          return ''
        }
      },
      ...mapState({
        languages: state => state.language.all,
        currentLocale: state => state.language.active.value
      })
    },
    methods: {
      saveMetadata: function (newDatas) {
        if (!newDatas.locale) {
          const value = newDatas
          newDatas = {
            value: value
          }
        }

        newDatas.id = this.id
        this.$emit('change', newDatas)
      }
    },
    mounted: function () {
      let initialValues = {}
      let initialValue = ''
      let index = 0

      this.languages.forEach((lang) => {
        const langVal = lang.value

        if (this.customMetadatas) {
          if (this.customMetadatas[langVal]) {
            initialValues[langVal] = this.customMetadatas[langVal]
          } else if ((this.customMetadatas === true || typeof this.customMetadatas === 'string') && index === 0) {
            initialValues[langVal] = this.customMetadatas
            initialValue = this.customMetadatas
          } else {
            initialValues[langVal] = ''
          }

          let field = {}
          field.name = this.fieldName
          field.value = initialValues[langVal]

          if (this.languages.length > 1) field.locale = langVal

          this.$store.commit(FORM.UPDATE_FORM_FIELD, field)
        }

        index++
      })

      this.initialValues = initialValues
      this.initialValue = initialValue
    },
    beforeDestroy: function () {
      this.$store.commit(FORM.REMOVE_FORM_FIELD, this.fieldName)
    }
  }
</script>

<style lang="scss" scoped>
  .mediaMetadata__checkbox {
    margin-top: 35px;
  }
</style>
