<template>
  <div class="locale">
    <template v-if="languages && languages.length && languages.length > 1">
    <div class="locale__item" v-for="(language, index) in languages" :key="language.value">
      <component v-bind:is="`${type}`" :data-lang="language.value"
        v-bind="attributesPerLang(language.value)"
        :name="`${attributes.name}[${language.value}]`"
        :fieldName="attributes.name"
        :locale="language"
        @localize="updateLocale"
        @change="updateValue(language.value, ...arguments)"
      ><slot></slot></component>
    </div>
    </template>
    <template v-else>
      <component v-bind:is="`${type}`"
        :name="attributes.name"
        v-bind="attributesNoLang()"
        @change="updateValue(false, ...arguments)"
      ><slot></slot></component>
    </template>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    name: 'A17Locale',
    props: {
      type: {
        type: String,
        default: 'text'
      },
      attributes: {
        type: Object,
        default: function () {
          return {}
        }
      },
      initialValues: {
        type: Object,
        default: function () {
          return {}
        }
      },
      initialValue: {
        type: String,
        default: ''
      }
    },
    computed: {
      ...mapState({
        currentLocale: state => state.language.active,
        languages: state => state.language.all
      })
    },
    watch: {
      initialValues: function (value) {
        console.log('WATCH initialValues')
        console.log(value)
      }
    },
    methods: {
      attributesPerLang: function (lang) {
        // for textfields set initial values using the initialValues prop
        if (this.initialValues[lang]) this.attributes.initialValue = this.initialValues[lang]

        return this.attributes
      },
      attributesNoLang: function () {
        // for textfields set initial values using the initialValue prop
        if (this.initialValue) this.attributes.initialValue = this.initialValue
        return this.attributes
      },
      updateLocale: function (oldValue) {
        this.$store.commit('switchLanguage', { oldValue })

        // auto focus new field
        this.$nextTick(function () {
          const currentLanguageItem = this.$el.querySelector('[data-lang="' + this.currentLocale.value + '"]')

          if (currentLanguageItem) {
            const field = currentLanguageItem.querySelector('input:not([disabled]), textarea:not([disabled]), select:not([disabled])')
            if (field) field.focus()
          }
        })

        this.$emit('localize', this.currentLocale)
      },
      updateValue: function (locale, newValue) {
        if (locale) {
          this.$emit('change', {
            locale: locale,
            value: newValue
          })
        } else {
          this.$emit('change', {
            value: newValue
          })
        }
      }
    }
  }
</script>
