import forEachNodelist from '../utils/forEachNodelist.js'
// Open Media library from the nav

const openMediaLibrary = function () {
  const bts = document.querySelectorAll('[data-medialib-btn]')

  function _triggerOpenMediaLibrary () {
    if (window.vm) {
      window.vm.openFreeMediaLibrary()
    }
  }

  // Toggle Click button
  if (bts.length) {
    forEachNodelist(bts, function (bt) {
      bt.addEventListener('click', function (e) {
        e.preventDefault()

        _triggerOpenMediaLibrary()
        bt.blur()
      })
    })
  }
}

export default openMediaLibrary
