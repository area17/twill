export default {
  methods: {
    openEditor (active = -1, section = false) {
      if (this.$root.$refs.editor) this.$root.$refs.editor.open(active, section)
    }
  }
}
