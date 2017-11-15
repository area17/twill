import * as types from '../mutation-types'

const state = {
  success: null,
  danger: null,
  info: null,
  warning: null
}

// getters
const getters = {
  notifByVariant: state => {
    return variant => state[variant]
  },
  notified: state => {
    return Object.keys(state).filter(key => state[key] !== null).length === 0
  }
}

const mutations = {
  [types.SET_NOTIF] (state, notif) {
    state[notif.variant] = notif.message
  },
  [types.CLEAR_NOTIF] (state, variant) {
    if (state[variant]) {
      state[variant] = null
    }
  }
}

export default {
  state,
  getters,
  mutations
}
