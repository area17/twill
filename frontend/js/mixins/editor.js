export default {
  methods: {
    openEditor: function (active = -1) {
      if (this.$root.$refs.editor) this.$root.$refs.editor.open(active)
    }
  }
}
