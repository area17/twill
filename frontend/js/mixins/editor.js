export default {
  methods: {
    openEditor (active = -1, editorName = false) {
      if (this.$root.$refs.editor) this.$root.$refs.editor.open(active, editorName)
    }
  }
}
