import * as types from '../mutation-types'

const state = {
  active: window.STORE.parentId || 0,
  all: window.STORE.parents || []
}

// getters
const getters = { }

const mutations = {
  [types.UPDATE_PARENT] (state, newValue) {
    if (newValue) state.active = newValue
    else state.active = 0
  }
}

export default {
  state,
  getters,
  mutations
}
