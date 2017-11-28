export default {
  data: function () {
    return {
      animation: 150,
      handle: '.drag__handle',
      ghostClass: 'sortable-ghost',
      chosenClass: 'sortable-chosen',
      dragClass: 'sortable-drag',
      scrollSensitivity: 30
    }
  },
  computed: {
    dragOptions: function () {
      let self = this
      return {
        animation: self.animation,
        handle: self.handle,
        ghostClass: self.ghostClass,
        chosenClass: self.chosenClass,
        dragClass: self.dragClass,
        scrollSensitivity: self.scrollSensitivity
      }
    }
  }
}
