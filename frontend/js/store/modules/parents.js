/**
 * Parents
 *
 * Parent / Child relationship for entities (pages, posts)
 */

import { PARENTS } from '../mutations'

const state = {
  /**
   * Id of the parent entity. O mean no parent
   * @type {Number}
   */
  active: window[import.meta.env.VITE_APP_NAME].STORE.parentId || 0,
  /**
   * Array of all the possible parents
   * @type {Array}
   */
  all: window[import.meta.env.VITE_APP_NAME].STORE.parents || []
}

// getters
const getters = { }

const mutations = {
  [PARENTS.UPDATE_PARENT] (state, newValue) {
    if (newValue) state.active = newValue
    else state.active = 0
  }
}

export default {
  state,
  getters,
  mutations
}
