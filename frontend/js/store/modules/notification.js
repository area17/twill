import { NOTIFICATION } from '../mutations'

const state = {
  success: null,
  info: null,
  warning: null,
  error: null
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
  [NOTIFICATION.SET_NOTIF] (state, notif) {
    state[notif.variant] = notif.message
  },
  [NOTIFICATION.CLEAR_NOTIF] (state, variant) {
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
