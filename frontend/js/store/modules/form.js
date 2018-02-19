import api from '../api/form'
import * as types from '../mutation-types'
import { getFormData, getFormFields } from '@/utils/getFormData.js'

const state = {
  loading: false,
  type: 'save',
  baseUrl: window.STORE.form.baseUrl || '',
  fields: window.STORE.form.fields || [],
  saveUrl: window.STORE.form.saveUrl || '',
  previewUrl: window.STORE.form.previewUrl || '',
  restoreUrl: window.STORE.form.restoreUrl || '',
  blockPreviewUrl: window.STORE.form.blockPreviewUrl || '',
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
    return getters.fieldsByName(name).length ? getters.fieldsByName(name)[0].value : ''
  }
}

const mutations = {
  [types.UPDATE_FORM_PERMALINK] (state, newValue) {
    if (newValue && newValue !== '') {
      state.permalink = newValue
    }
  },
  [types.EMPTY_FORM_FIELDS] (state, status) {
    state.fields = []
  },
  [types.REPLACE_FORM_FIELDS] (state, fields) {
    state.fields = fields
  },
  [types.UPDATE_FORM_FIELD] (state, field) {
    let fieldValue = field.locale ? {} : null
    const fieldIndex = state.fields.findIndex(function (f) {
      return f.name === field.name
    })

    // Update existing form field
    if (fieldIndex !== -1) {
      if (field.locale) fieldValue = state.fields[fieldIndex].value
      // remove existing field
      state.fields.splice(fieldIndex, 1)
    }

    if (field.locale) fieldValue[field.locale] = field.value
    else fieldValue = field.value

    state.fields.push({
      name: field.name,
      value: fieldValue
    })
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
  replaceFormData ({ commit, state, getters, rootState }, endpoint) {
    return new Promise((resolve, reject) => {
      commit(types.CLEAR_FORM_ERRORS)
      commit(types.CLEAR_NOTIF, 'error')

      api.get(endpoint, function (successResponse) {
        commit(types.UPDATE_FORM_LOADING, false)
        commit(types.REPLACE_FORM_FIELDS, successResponse.data)
        resolve()
      }, function (errorResponse) {
        commit(types.UPDATE_FORM_LOADING, false)
        commit(types.SET_FORM_ERRORS, errorResponse.response.data)
        reject(errorResponse)
      })
    })
  },
  updateFormInListing ({ commit, state, getters, rootState }, options) {
    return new Promise((resolve, reject) => {
      commit(types.CLEAR_FORM_ERRORS)
      commit(types.CLEAR_NOTIF, 'error')

      const data = getFormFields(rootState)

      api.post(options.endpoint, data, function (successResponse) {
        commit(types.UPDATE_FORM_LOADING, false)

        if (successResponse.data.hasOwnProperty('redirect') && options.redirect) {
          window.location.replace(successResponse.data.redirect)
        }

        commit(types.SET_NOTIF, { message: successResponse.data.message, variant: successResponse.data.variant })
        resolve()
      }, function (errorResponse) {
        commit(types.UPDATE_FORM_LOADING, false)
        commit(types.SET_FORM_ERRORS, errorResponse.response.data)
        commit(types.SET_NOTIF, { message: 'Your submission could not be validated, please fix and retry', variant: 'error' })
        reject(errorResponse)
      })
    })
  },
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
