// Show / Hide the env colored line when scrolling after the header

import debounce from 'lodash/debounce'

const showEnvLine = function () {
  let lastScrollPos = 0
  let ticking = false
  const offset = 170 - 3
  const htmlElement = document.documentElement

  const klass = 's--env'

  function _scroll () {
    lastScrollPos = window.pageYOffset

    if (!ticking) {
      window.requestAnimationFrame(function () {
        _refresh()
      })
    }

    ticking = true
  }

  function _refresh () {
    if (lastScrollPos > offset) htmlElement.classList.add(klass)
    else htmlElement.classList.remove(klass)

    ticking = false
  }

  window.addEventListener('scroll', function () { _scroll() })
  window.addEventListener('resize', debounce(function () { _scroll() }))

  _scroll()
}

export default showEnvLine
