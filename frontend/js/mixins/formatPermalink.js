import { mapState } from 'vuex'
import { FORM } from '@/store/mutations'
import a17VueFilters from '@/utils/filters.js'

export default {
  filters: a17VueFilters,
  computed: {
    ...mapState({
      currentLocale: state => state.language.active
    })
  },
  methods: {
    formatPermalink: function (newValue) {
      const permalinkRef = this.$refs.permalink

      if (!permalinkRef) return

      if (newValue) {
        let text = ''

        if (newValue.value && typeof newValue.value === 'string') {
          text = newValue.value
        } else if (typeof newValue === 'string') {
          text = newValue
        }

        const slug = this.$options.filters.slugify(text)

        let field = {
          name: permalinkRef.attributes ? permalinkRef.attributes.name : permalinkRef.name,
          value: slug
        }

        if (newValue.locale) {
          field.locale = newValue.locale
        } else {
          field.locale = this.currentLocale.value
        }

        // Update value in the store
        this.$store.commit(FORM.UPDATE_FORM_FIELD, field)
      }
    }
  }
}
