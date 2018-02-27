import { MODALEDITION } from '@/store/mutations'

const state = {
  action: '#',
  mode: 'create' // 'create' or 'update'
}

// getters
const getters = {

}

const mutations = {
  [MODALEDITION.UPDATE_MODAL_ACTION] (state, newAction) {
    state.action = newAction
  },
  [MODALEDITION.UPDATE_MODAL_MODE] (state, newMode) {
    state.mode = newMode
  }
}

export default {
  state,
  getters,
  mutations
}
