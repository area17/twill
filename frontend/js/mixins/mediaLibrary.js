export default {
  props: {
    type: {
      type: String,
      default: 'image'
    }
  },
  methods: {
    openMediaLibrary: function (max = 1, name = this.name, index = -1) {
      this.$store.commit('updateMediaConnector', name)
      this.$store.commit('updateMediaType', this.type)
      this.$store.commit('updateReplaceIndex', index)
      this.$store.commit('updateMediaMax', max)
      if (this.$root.$refs.mediaLibrary) this.$root.$refs.mediaLibrary.open()
    }
  }
}
