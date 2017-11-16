export default {
  props: {
    type: {
      type: String,
      default: 'image'
    }
  },
  methods: {
    openMediaLibrary: function (max = 1) {
      this.$store.commit('updateMediaConnector', this.name)
      this.$store.commit('updateMediaType', this.type)
      this.$store.commit('updateMediaMax', max)
      if (this.$root.$refs.mediaLibrary) this.$root.$refs.mediaLibrary.open()
    }
  }
}
