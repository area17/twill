// SVGs

// Should output : '<span class="icon icon--${id}"><svg><title>id</title><use xlink:href="#icon--${id}"></use></svg></span>';
// <svg class="icon icon--${id}"><title>id</title><use xlink:href="#icon--${id}"></use></svg> if node is already a svg

export function addSvg (el, binding, vnode) {
  let classNames = ['icon']
  const id = binding.expression || vnode.data.attrs.symbol
  let svg = el

  // span or svg ?
  if (vnode.tag === 'span') {
    // add SVG element
    svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg')
    el.appendChild(svg)
  }

  // add classes to wrapper
  classNames.push(`icon--${id}`)
  classNames.forEach(function (className) {
    el.classList.add(className)
  })

  // add title to SVGs
  const title = document.createElementNS('http://www.w3.org/2000/svg', 'title')

  title.textContent = id
  svg.appendChild(title)

  // Add the <use> element to <svg>
  const href = `#${id}`
  const use = document.createElementNS('http://www.w3.org/2000/svg', 'use')

  use.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', href)
  svg.appendChild(use)
}

export function removeSvg (el) {
  const svg = el.querySelector('svg')

  // remove svg
  if (svg) svg.parentNode.removeChild(svg)

  // clean up classes
  const classNames = el.className.split(' ').filter(function (c) {
    return c.indexOf('icon') === 0
  })

  classNames.forEach(function (className) {
    el.classList.remove(className)
  })
}
