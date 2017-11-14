// Show / Hide the menu on mobile devices

const navToggle = function () {
  let isAnimating = false
  let isActive = false
  let lastScrollPos = 0
  const htmlElement = document.documentElement
  const bts = document.querySelectorAll('[data-ham-btn]')
  const wrapper = document.querySelector('.a17')

  const klass = 's--nav'

  function _triggerOpenNav () {
    if (isAnimating) return false
    if (isActive) return false

    isAnimating = true

    lastScrollPos = window.pageYOffset

    htmlElement.classList.add(klass)
    wrapper.style.top = '-' + lastScrollPos + 'px'

    document.addEventListener('keydown', _escNav, false)

    isActive = true
    isAnimating = false
  }

  function _triggerCloseNav () {
    if (isAnimating) return false
    if (!isActive) return false

    isAnimating = true

    htmlElement.classList.remove(klass)
    wrapper.style.top = ''

    document.removeEventListener('keydown', _escNav, false)

    window.scrollTo(0, lastScrollPos)
    lastScrollPos = 0

    isActive = false
    isAnimating = false
  }

  function _escNav (e) {
    if (e.keyCode === 27 && isActive) _triggerCloseNav() /* esc key */
  }

  // Toggle Click button
  if (bts.length) {
    bts.forEach(function (bt) {
      bt.addEventListener('click', function (e) {
        if (!isActive) _triggerOpenNav()
        else _triggerCloseNav()

        bt.blur()
      })
    })
  }
}

export default navToggle
