import { mapState } from 'vuex'

export default {
  props: {
    locale: {
      default: null
    }
  },
  computed: {
    hasLocale: function () {
      return this.locale != null
    },
    hasCurrentLocale: function () {
      return this.currentLocale != null
    },
    isCurrentLocale: function () {
      if (this.hasLocale && this.hasCurrentLocale) {
        return this.locale.value === this.currentLocale.value
      } else {
        return true
      }
    },
    displayedLocale: function () {
      if (this.hasLocale) return this.locale.shortlabel
      else return false
    },
    ...mapState({
      currentLocale: state => state.language.active,
      languages: state => state.language.all
    })
  },
  methods: {
    onClickLocale: function () {
      this.$emit('localize', this.locale)
    },
    updateLocale: function (oldValue) {
      this.$emit('localize', oldValue)
    }
  }
}
