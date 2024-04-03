import { addSvg, removeSvg } from '@/utils/svg.js'

export default {
  install (app, opts = {}) {
    const dir = {
      bind (el, binding, vnode) {
        addSvg(el, binding, vnode)
      },
      componentUpdated: function (el, binding, vnode, oldVnode) {
        removeSvg(el)
        addSvg(el, binding, vnode)
      },
      inserted: function (el, binding, vnode) {
      },
      unbind: function (el, binding, vnode) {
        // removeSvg(el)
      }
    }
    app.directive('svg', dir)
  }
}
