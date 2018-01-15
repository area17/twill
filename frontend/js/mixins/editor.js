export default {
  methods: {
    openEditor: function (active = -1) {
      this.$store.commit('activateBlock', active)
      if (this.$root.$refs.editor) this.$root.$refs.editor.open()
    }
  }
}
