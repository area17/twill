import * as types from '../mutation-types'

const state = {
  title: window.STORE.form.title || '',
  permalink: window.STORE.form.permalink || '',
  baseUrl: window.STORE.form.baseUrl || '',
  fields: window.STORE.form.fields || [],
}

// getters
const getters = {}

const mutations = {
  [types.UPDATE_FORM_TITLE] (state, newValue) {
    if (newValue && newValue !== '') {
      state.title = newValue
    }
  },
  [types.UPDATE_FORM_PERMALINK] (state, newValue) {
    if (newValue && newValue !== '') {
      state.permalink = newValue
    }
  },
  [types.UPDATE_FORM_FIELD] (state, field) {
    const fieldToUpdate = state.fields.filter(function (f) {
      return f.name === field.name
    })

    // Update existing form field
    if (fieldToUpdate.length) {
      if (field.locale) {
        fieldToUpdate[0].value[field.locale] = field.value
      } else {
        fieldToUpdate[0].value = field.value
      }
    } else {
      // Or Create a new form field
      if (field.locale) {
        const localeValue = {}
        localeValue[field.locale] = field.value

        state.fields.push({
          name: field.name,
          value: localeValue
        })
      } else {
        state.fields.push({
          name: field.name,
          value: field.value
        })
      }
    }
  },
  [types.REMOVE_FORM_FIELD] (state, fieldName) {
    state.fields.forEach(function (field, index) {
      if (field.name === fieldName) state.fields.splice(index, 1)
    })
  }
}





    }
  }
}

export default {
  state,
  getters,
  mutations
}
