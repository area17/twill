import Sticky from '../utils/sticky.js'

// some default options here (see the plugin options)
export const defaultOptions = {
}

export default {
  install (app, opts = {}) {
    const vsticky = {
      options: defaultOptions,
      beforeMount: function (el, binding, vnode) {
        const sticky = el._sticky = new Sticky(el, vsticky.options)
        sticky._vueEl = el
      },

      updated: function (el, binding, vnode) {
        el._sticky.refresh()
      },

      mounted: function (el, binding, vnode) {
      },

      unbind: function (el, binding, vnode) {
        el._sticky.dispose()
      }
    }

    app.directive('sticky', vsticky)
  }
}
