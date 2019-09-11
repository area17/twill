<template>
  <div class="locale">
    <template v-if="languages && languages.length && languages.length > 0">
    <div class="locale__item" v-for="language in languages" :key="language.value">
      <component v-bind:is="`${type}`" :data-lang="language.value"
        v-bind="attributesPerLang(language.value)"
        :name="`${attributes.name}[${language.value}]`"
        :fieldName="attributes.name"
        :locale="language"
        @localize="updateLocale"
        @change="updateValue(language.value, ...arguments)"
        @blur="$emit('blur')"
        @focus="$emit('focus')"
      ><slot></slot></component>
    </div>
    </template>
    <template v-else>
      <component v-bind:is="`${type}`"
        :name="attributes.name"
        v-bind="attributesNoLang()"
        @change="updateValue(false, ...arguments)"
        @blur="$emit('blur')"
        @focus="$emit('focus')"
      ><slot></slot></component>
    </template>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { LANGUAGE } from '@/store/mutations'

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
      isRequired: {
        type: Boolean,
        default: function () {
          return this.attributes.required || false
        }
      }
    },
    computed: {
      ...mapState({
        currentLocale: state => state.language.active,
        languages: state => state.language.all
      })
    },
    methods: {
      attributesPerLang: function (lang) {
        const language = this.languages.find(l => l.value === lang)

        let attributes = Object.assign({}, this.attributes)
        // for textfields set initial values using the initialValues prop
        if (this.initialValues && typeof this.initialValues === 'object' && this.initialValues[lang]) {
          attributes.initialValue = this.initialValues[lang]
        } else if (!attributes.initialValue) {
          attributes.initialValue = ''
        }

        attributes.required = !!language.published && this.isRequired

        return attributes
      },
      attributesNoLang: function () {
        let attributes = Object.assign({}, this.attributes)
        // for textfields set initial values using the initialValue prop
        if (this.initialValue) attributes.initialValue = this.initialValue
        return attributes
      },
      updateLocale: function (oldValue) {
        this.$store.commit(LANGUAGE.SWITCH_LANG, { oldValue })

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
