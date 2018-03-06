/**
 * Attributes
 *
 * Create new attributes (categories, tags...) in forms
 */

import Vue from 'vue'
import { ATTRIBUTES } from '../mutations'

const state = {
  options: {}
}

// getters
const getters = {
  optionsByName (state) {
    return name => state.options[name]
  }
}

const mutations = {
  [ATTRIBUTES.EMPTY_OPTIONS] (state, name) {
    if (state.options[name]) {
      Vue.delete(state.options, name)
    }
  },
  [ATTRIBUTES.REPLACE_OPTIONS] (state, attributes) {
    const name = attributes.name
    const options = attributes.options

    // Update existing form field
    if (state.options[name]) Vue.delete(state.options, name)

    Vue.set(state.options, name, options)
  }
}

const actions = {}

export default {
  state,
  getters,
  mutations,
  actions
}
