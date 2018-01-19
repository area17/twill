import Vue from 'vue'
import api from '../api/form'
import * as types from '../mutation-types'
import { getFormData } from '@/utils/getFormData.js'

const state = {
  loading: false,
  type: 'save',
  title: window.STORE.form.title || '',
  permalink: window.STORE.form.permalink || '',
  baseUrl: window.STORE.form.baseUrl || '',
  fields: window.STORE.form.fields || [],
  saveUrl: window.STORE.form.saveUrl || '',
  errors: {}
}

// getters
const getters = {
  fieldsByName (state) {
    return name => state.fields.filter(function (field) {
      return field.name === name
    })
  },
  fieldValueByName: (state, getters) => name => { // want to use getters
    return getters.fieldsByName(name).length ? getters.fieldsByName(name)[0].value : false
  }
}

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
        Vue.set(fieldToUpdate[0].value, field.locale, field.value)
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
  [types.REFRESH_FORM_FIELD] (state, field) {
    const fieldIndex = state.fields.findIndex(function (f) {
      return f.name === field.name
    })

    if (fieldIndex !== -1) {
      const fieldToRefresh = state.fields[fieldIndex].value
      state.fields[fieldIndex].value = null
      state.fields[fieldIndex].value = fieldToRefresh
    }
  },
  [types.REMOVE_FORM_FIELD] (state, fieldName) {
    state.fields.forEach(function (field, index) {
      if (field.name === fieldName) state.fields.splice(index, 1)
    })
  },
  [types.UPDATE_FORM_LOADING] (state, loading) {
    state.loading = !state.loading
  },
  [types.SET_FORM_ERRORS] (state, errors) {
    state.errors = errors
  },
  [types.CLEAR_FORM_ERRORS] (state) {
    state.errors = []
  },
  [types.UPDATE_FORM_SAVE_TYPE] (state, type) {
    state.type = type
  }
}

const actions = {
  saveFormData ({ commit, state, getters, rootState }, saveType) {
    commit(types.CLEAR_FORM_ERRORS)
    commit(types.CLEAR_NOTIF, 'error')

    // update or create etc...
    commit(types.UPDATE_FORM_SAVE_TYPE, saveType)

    // we can now create our submitted data object out of:
    // - our just created fields object,
    // - publication properties
    // - selected medias and browsers
    // - created blocks and repeaters
    const data = getFormData(rootState)

    api.save(state.saveUrl, data, function (successResponse) {
      commit(types.UPDATE_FORM_LOADING, false)

      if (successResponse.data.hasOwnProperty('redirect')) {
        window.location.replace(successResponse.data.redirect)
      }

      commit(types.SET_NOTIF, { message: successResponse.data.message, variant: successResponse.data.variant })
    }, function (errorResponse) {
      commit(types.UPDATE_FORM_LOADING, false)
      commit(types.SET_FORM_ERRORS, errorResponse.response.data)
      commit(types.SET_NOTIF, { message: 'Your submission could not be validated, please fix and retry', variant: 'error' })
    })
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
