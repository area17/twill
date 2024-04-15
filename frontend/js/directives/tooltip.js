import Tooltip from '../utils/tooltip.js'

// some default options here (see the plugin options)
export const defaultOptions = {
}

export default {
  install (app, opts = {}) {
    const vtooltip = {
      options: defaultOptions,
      beforeMount: function (el, binding, vnode) {
        if (!el._tooltip) {
          const tooltip = el._tooltip = new Tooltip(el, vtooltip.options)
          tooltip._vueEl = el
        }
      },
      updated: function (el, binding, vnode, oldVnode) {
        if (el._tooltip) {
          el._tooltip.dispose()

          const tooltip = el._tooltip = new Tooltip(el, vtooltip.options)
          tooltip._vueEl = el
        }
      },
      mounted: function (el, binding, vnode) {
      },
      unbind: function (el, binding, vnode) {
        if (el._tooltip) {
          el._tooltip.dispose()
        }
      }
    }

    app.directive('tooltip', vtooltip)
  }
}
