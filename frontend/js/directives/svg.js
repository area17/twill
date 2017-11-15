export default {
  install (Vue, opts = {}) {
    const dir = {
      bind (el, binding, vnode) {
        // Should output : '<span class="icon icon--${id}"><svg><title>id</title><use xlink:href="#icon--${id}"></use></svg></span>';
        // <svg><title>id</title><use xlink:href="#icon--${id}"></use></svg> if node is a svg already

        let classNames = ['icon']
        const id = binding.expression || vnode.data.attrs.symbol
        let svg = el

        // span or svg ?
        if (vnode.tag === 'span') {
          classNames.push(`icon--${id}`)
          classNames.forEach(function (className) {
            el.classList.add(className)
          })

          // add SVG element
          svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg')
          el.appendChild(svg)
        }

        // add title to SVGs
        const title = document.createElementNS('http://www.w3.org/2000/svg', 'title')

        title.textContent = id
        svg.appendChild(title)

        // Add the <use> element to <svg>
        const href = `#${id}`
        const use = document.createElementNS('http://www.w3.org/2000/svg', 'use')

        use.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', href)
        svg.appendChild(use)
      },
      inserted: function (el, binding, vnode) {
      },
      unbind: function (el, binding, vnode) {
        console.log('Unbind SVG')
        console.log(el)
        console.log(vnode)

        const svg = el.querySelector('svg')
        if (svg) svg.parentNode.removeChild(svg)

        el.className.remove('icon')
      }
    }

    Vue.directive('svg', dir)
  }
}
