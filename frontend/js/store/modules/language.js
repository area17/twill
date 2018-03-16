/**
 * Language
 *
 * Switch between languages
 * Publish, unpublish languages
 */

import { LANGUAGE } from '../mutations'

const state = {
  /**
   * Array of all the languages available
   * @type {Array.Object}
   */
  all: window.STORE.languages.all || [],
  /**
   * Array of all the languages available (initial value (not to be modified))
   * @type {Array.Object}
   */
  initialAll: window.STORE.languages.all || [],
  /**
   * The language you are currently editing
   * @type {Object}
   */
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

  [LANGUAGE.PUBLISH_SINGLE_LANG] (state, newValue) {
    function isMatchingLocale (language) {
      return language.value === newValue
    }

    const index = state.all.findIndex(isMatchingLocale)
    state.all[index].published = !state.all[index].published
  },
  [LANGUAGE.REPLACE_LANGUAGES] (state, newValue) {
    state.all = newValue
  },
  [LANGUAGE.RESET_LANGUAGES] (state) {
    state.all = state.initialAll
  }
}

export default {
  state,
  getters,
  mutations
}
