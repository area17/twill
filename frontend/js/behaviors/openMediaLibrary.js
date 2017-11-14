// Open Media library from the nav

const openMediaLibrary = function () {
  const bts = document.querySelectorAll('[data-medialib-btn]')

  function _triggerOpenMediaLibrary () {
    if (Window.vm) {
      if (Window.vm.$refs.mediaLibrary) {
        Window.vm.$refs.mediaLibrary.open()
      }
    }
  }

  // Toggle Click button
  if (bts.length) {
    bts.forEach(function (bt) {
      bt.addEventListener('click', function (e) {
        e.preventDefault()

        _triggerOpenMediaLibrary()
        bt.blur()
      })
    })
  }
}

export default openMediaLibrary
