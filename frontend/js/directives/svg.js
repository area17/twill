import { addSvg, removeSvg } from '@/utils/svg.js'

export default {
  install (app, opts = {}) {
    const dir = {
      beforeMount (el, binding, vnode) {
        addSvg(el, binding, vnode)
      },
      componentUpdated: function (el, binding, vnode, oldVnode) {
        removeSvg(el)
        addSvg(el, binding, vnode)
      },
      mounted: function (el, binding, vnode) {
      },
      unbind: function (el, binding, vnode) {
        // removeSvg(el)
      }
    }
    app.directive('svg', dir)
  }
}
