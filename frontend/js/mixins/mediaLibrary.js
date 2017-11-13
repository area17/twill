export default {
  props: {
    type: {
      type: String,
      default: 'Image'
    }
  },
  methods: {
    openMediaLibrary: function (max = 1) {
      this.$store.commit('updateMediaConnector', this.name)
      this.$store.commit('updateMediaType', this.type)
      this.$store.commit('updateMediaMax', max)
      this.$root.$refs.mediaLibrary.open()
    }
  }
}
