import * as types from '../mutation-types'

const state = {
  action: '#',
  mode: 'create'
}

// getters
const getters = {

}

const mutations = {
  [types.UPDATE_MODAL_ACTION] (state, newAction) {
    state.action = newAction
  },
  [types.UPDATE_MODAL_MODE] (state, newMode) {
    state.mode = newMode
  }
}

export default {
  state,
  getters,
  mutations
}
