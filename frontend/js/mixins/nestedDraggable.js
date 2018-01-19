export default {
  methods: {
    onStart: function (event) {
      document.querySelector('.datatable').classList.add('datatable--dragging')
    },
    onEnd: function (event) {
      document.querySelector('.datatable').classList.remove('datatable--dragging')
    }
  }
}
