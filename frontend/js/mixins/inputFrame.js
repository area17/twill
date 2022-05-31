function arrayToSentence (arr) {
  const length = arr.length
  return arr.reduce(function (a, b, c) {
    return a + (c - 1 === length ? ', ' : ' and ') + b
  })
}

export default {
  props: {
    label: {
      type: String,
      default: ''
    },
    labelFor: {
      type: String,
      default: ''
    },
    size: {
      type: String,
      default: 'large' // large, small
    },
    variant: {
      type: String,
      default: '' // free string
    },
    fixedErrorKey: {
      type: String
    },
    note: {
      type: String,
      default: ''
    }
  },
  computed: {
    errorKey () {
      if (this.fixedErrorKey) {
        return this.hasLocale ? (this.fixedErrorKey.replace('[', '.').replace(']', '')) : this.fixedErrorKey
      }
      return this.hasLocale ? (this.name.replace('[', '.').replace(']', '')) : this.name
    },
    errorLocales () {
      if (!this.hasLocale) return []

      const errorKeyWithoutLocale = this.errorKey.substr(0, this.errorKey.indexOf('.'))

      const locales = []

      const supportedLanguages = this.$store.state.language.all.map(lang => lang.value)

      Object.keys(this.$store.state.form.errors).forEach((error) => {
        if (error.substr(0, error.indexOf('.')) === errorKeyWithoutLocale) {
          const cleaned = error.substr(error.indexOf('.') + 1, error.length)

          if (supportedLanguages.includes(cleaned)) {
            locales.push(cleaned)
          }
        }
      }, [])

      return locales
    },
    otherLocalesError () {
      return this.errorLocales.filter((locale) => {
        return locale !== this.currentLocale.value
      }).length
    },
    errorMessageLocales () {
      return arrayToSentence(this.errorLocales.map((locale) => {
        return this.languages.find(l => l.value === locale).label
      })) + ' language' + (this.errorLocales.length > 1 ? 's' : '') + ' missing details.'
    },
    errorMessage () {
      return this.error ? this.$store.state.form.errors[this.errorKey][0] : ''
    },
    error () {
      return this.$store.state.form ? Object.keys(this.$store.state.form.errors).includes(this.errorKey) : false
    }
  }
}
