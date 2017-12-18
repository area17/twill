<template>
  <div class="locale">
    <template v-if="languages && languages.length">
    <div class="locale__item" v-for="(language, index) in languages" :key="language.value">
      <component v-bind:is="`${type}`" :data-lang="language.value"
        v-bind="attributes"
        :name="`${attributes.name}[${language.value}]`"
        :fieldName="attributes.name"
        :locale="language"
        @localize="updateLocale"
        @change="updateValue(language.value, ...arguments)"
      ><slot></slot></component>
    </div>
    </template>
    <template v-else>
      <component v-bind:is="`${type}`" v-bind="attributes"><slot></slot></component>
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
      }
    },
    computed: {
      ...mapState({
        currentLocale: state => state.language.active,
        languages: state => state.language.all
      })
    },
    methods: {
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
        // const name = this.attributes.name
        // TODO : emit change again here
      }
    }
  }
</script>
