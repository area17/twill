// active scroll nav behavior
const ACTIVE_CLASS = 'text-link'
const LIST_ACTIVE = 'is-active'
const nav = document.querySelector('.chapters-nav-fixed')
const headerHeight = 80

let scrollPos = window.scrollY

if (nav) {
  const chapterItems = [
    ...document.querySelectorAll('.markdown h2, .markdown h3')
  ]
  const navItems = [...nav.querySelectorAll('li')]

  // helper function to get the correct titles href
  const _getActiveHref = (titles, pos) => {
    const current = []
    titles.forEach(el => {
      // get height to top of page from title
      const offset = el.getBoundingClientRect()
      const top =
        offset.top +
        (document.documentElement.scrollTop || document.body.scrollTop)

      // if we are past the accepted height of the header push that item to an array
      if (top < pos + headerHeight + 40) {
        if (el.querySelector('a')){
          current.push(`#${el.querySelector('a').getAttribute('id')}`)
        }
      }
    })

    // pluck the last (most recent) item from that array and serve that :)
    // OR if undefined, return the first item
    return current[current.length - 1]
      ? current[current.length - 1]
      : chapterItems.length > 0 ? (chapterItems[0].querySelector('a') ? `#${chapterItems[0].querySelector('a').getAttribute('id')}` : -1) : -1
  }

  const setActiveNav = pos => {
    const active = _getActiveHref(chapterItems, pos)

    navItems.forEach(elem => {
      const linkEl = elem.querySelector('a')
      const listEl = elem
      const hash = new URL(linkEl.href).hash
      if (hash === active) {
        linkEl.classList.add(ACTIVE_CLASS)
        listEl.classList.add(LIST_ACTIVE)
      } else {
        linkEl.classList.remove(ACTIVE_CLASS)
        listEl.classList.remove(LIST_ACTIVE)
      }
    })
    const activeElem = nav.querySelector('.is-active')
    // check the sum of all these parents are ul - then it was a nested child
    if (
      activeElem && activeElem.parentElement.parentElement.parentElement.nodeName === 'UL'
    ) {
      activeElem.parentElement.parentElement
        .querySelector('a')
        .classList.add(ACTIVE_CLASS)
    }
  }

  // trigger once on init to set value
  setActiveNav(scrollPos)

  // use animation frame for optimized scroll
  document.addEventListener('scroll', () => {
    let ticking = false
    scrollPos = window.scrollY
    if (!ticking) {
      window.requestAnimationFrame(() => {
        setActiveNav(scrollPos)
        ticking = false
      })
      ticking = true
    }
  })
}
