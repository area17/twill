import Tooltip from '../utils/tooltip.js'

// some default options here (see the plugin options)
export const defaultOptions = {
}

export default {
  install (Vue, opts = {}) {
    const vtooltip = {
      options: defaultOptions,
      bind: function (el, binding, vnode) {
        if (!el._tooltip) {
          const tooltip = el._tooltip = new Tooltip(el, vtooltip.options)
          tooltip._vueEl = el
        }
      },
      componentUpdated: function (el, binding, vnode, oldVnode) {
        if (el._tooltip) {
          el._tooltip.dispose()

          const tooltip = el._tooltip = new Tooltip(el, vtooltip.options)
          tooltip._vueEl = el
        }
      },
      inserted: function (el, binding, vnode) {
      },
      unbind: function (el, binding, vnode) {
        if (el._tooltip) {
          el._tooltip.dispose()
        }
      }
    }

    Vue.directive('tooltip', vtooltip)
  }
}
