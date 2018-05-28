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
    note: {
      type: String,
      default: ''
    }
  },
  computed: {
    errorKey () {
      return this.hasLocale ? (this.name.replace('[', '.').replace(']', '')) : this.name
    },
    errorLocales () {
      if (!this.hasLocale) return []

      const errorKeyWithoutLocale = this.errorKey.substr(0, this.errorKey.indexOf('.'))

      const locales = []

      Object.keys(this.$store.state.form.errors).forEach((error) => {
        if (error.substr(0, error.indexOf('.')) === errorKeyWithoutLocale) {
          locales.push(error.substr(error.indexOf('.') + 1, error.length))
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
      let message = this.error ? this.$store.state.form.errors[this.errorKey][0] : ''
      return message.endsWith('is required.') && !this.errorKey.startsWith('block') ? '' : message
    },
    error () {
      return this.$store.state.form ? Object.keys(this.$store.state.form.errors).includes(this.errorKey) : false
    }
  }
}
