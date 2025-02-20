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
    isLocaleRTL: function () {
      /* List of RTL locales */
      /*
        ar : Arabic
        arc : Aramaic
        dv : Divehi
        fa : Persian
        ha : Hausa
        he : Hebrew
        khw : Khowar
        ks : Kashmiri
        ku : Kurdish
        ps : Pashto
        ur : Urdu
        yi : Yiddish
      */
      const rtlLocales = ['ar', 'arc', 'dv', 'fa', 'ha', 'he', 'khw', 'ks', 'ku', 'ps', 'ur', 'yi']
      if (this.hasLocale) return rtlLocales.includes(this.locale.shortlabel.toLowerCase())
      else return false
    },
    dirLocale: function () {
      if (this.direction && this.direction !== 'auto') {
        return this.direction;
      }
      return (this.isLocaleRTL ? 'rtl' : 'auto')
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
