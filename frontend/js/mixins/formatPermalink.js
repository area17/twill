import a17VueFilters from '@/utils/filters.js'

export default {
  filters: a17VueFilters,
  methods: {
    formatPermalink: function (newValue) {
      if (newValue.value) {
        const slug = this.$options.filters.slugify(newValue.value)

        // Update value in the store
        let field = {}
        field.name = this.$refs.permalink.attributes.name
        field.value = slug
        if (newValue.locale) field.locale = newValue.locale
        this.$store.commit('updateFormField', field)
        this.$store.commit('refreshFormFieldUI', field)
      }
    }
  }
}
