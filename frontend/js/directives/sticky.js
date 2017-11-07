import Sticky from '../utils/sticky.js'

// some default options here (see the plugin options)
export const defaultOptions = {
}

export default {
  install (Vue, opts = {}) {
    const vsticky = {
      options: defaultOptions,
      bind: function (el, binding, vnode) {
        const sticky = el._sticky = new Sticky(el, vsticky.options)
        sticky._vueEl = el
      },

      componentUpdated: function (el, binding, vnode) {
        el._sticky.refresh()
      },

      inserted: function (el, binding, vnode) {
      },

      unbind: function (el, binding, vnode) {
        el._sticky.dispose()
      }
    }

    Vue.directive('sticky', vsticky)
  }
}
