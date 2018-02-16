import { LANGUAGE } from '../mutations'

const state = {
  all: window.STORE.languages.all || [],
  active: window.STORE.languages.active || window.STORE.languages.all[0] || {}
}

// getters
const getters = {
  publishedLanguages: state => {
    return state.all.filter(language => language.published)
  }
}

const mutations = {
  [LANGUAGE.SWITCH_LANG] (state, { oldValue }) {
    function isMatchingLocale (language) {
      return language.value === oldValue.value
    }

    const index = state.all.findIndex(isMatchingLocale)
    const newIndex = index < (state.all.length - 1) ? (index + 1) : 0

    state.active = state.all[newIndex]
  },

  [LANGUAGE.UPDATE_LANG] (state, newValue) {
    function isMatchingLocale (language) {
      return language.value === newValue
    }

    const index = state.all.findIndex(isMatchingLocale)
    state.active = state.all[index]
  },

  [LANGUAGE.PUBLISH_LANG] (state, publishedValues) {
    state.all.forEach(function (language) {
      language.published = !!publishedValues.includes(language.value)
    })
  },

  [types.PUBLISH_SINGLE_LANG] (state, newValue) {
    function isMatchingLocale (language) {
      return language.value === newValue
    }

    const index = state.all.findIndex(isMatchingLocale)
    state.all[index].published = !state.all[index].published
  }

}

export default {
  state,
  getters,
  mutations
}
