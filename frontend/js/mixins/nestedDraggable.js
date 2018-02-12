export default {
  computed: {
    updateTracker () {
      return this.$store.state.datatable.updateTracker
    }
  },
  methods: {
    onStart: function (event) {
      document.querySelector('.datatable').classList.add('datatable--dragging')
    },
    onEnd: function (event) {
      document.querySelector('.datatable').classList.remove('datatable--dragging')
    },
    saveNewTree: function (isChangingParents) {
      let self = this
      const isNestedAction = isChangingParents ? true : self.nested
      const action = isNestedAction ? 'setDatatableNestedDatas' : 'setDatatableDatas'

      function save () {
        self.$store.commit('updateDatableTracker', 0)
        self.$store.dispatch(action)
      }

      // Proof of concepts
      if (isChangingParents) {
        // 2 moves need to happen so we can save the new tree (1 move to remove from list and a second to add to a new list)
        self.$store.commit('updateDatableTracker', 1)
        if (self.updateTracker >= 2) save()
      } else {
        // reorder rows
        save()
      }
    }
  }
}
