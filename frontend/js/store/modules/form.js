/**
 * Form
 *
 * Save all the fields pf the form
 */

import api from '../api/form'
import { getFormData, getFormFields } from '@/utils/getFormData.js'
import { FORM, NOTIFICATION, LANGUAGE } from '../mutations'
import * as ACTIONS from '@/store/actions'
import { PUBLICATION, REVISION } from '@/store/mutations'

const state = {
  /**
   * Loading state of the form when submitting
   * @type {Boolean}
   */
  loading: false,
  /**
   * Type of submit : can be any value from state.publication.submitOptions
   * @type {String}
   */
  type: 'save',
  /**
   * Form errors after submitting
   * @type {Object}
   */
  baseUrl: window.STORE.form.baseUrl || '',
  /**
   * All the fields that need to be saved
   * @type {Array}
   */
  fields: window.STORE.form.fields || [],
  /**
   * Url to save/update the form
   * @type {String}
   */
  saveUrl: window.STORE.form.saveUrl || '',
  /**
   * Url to get a full preview of the form datas
   * @type {String}
   */
  previewUrl: window.STORE.form.previewUrl || '',
  /**
   * Url to restore previous form datas
   * @type {String}
   */
  restoreUrl: window.STORE.form.restoreUrl || '',
  /**
   * Url to get only the preview of a block
   * @type {String}
   */
  blockPreviewUrl: window.STORE.form.blockPreviewUrl || '',
  /**
   * Form errors after submitting
   * @type {Object}
   */
  errors: {},
  /**
   * Is this a custom form (that will let the browser submit the form instead of hooking up the submit event)
   * @type {Bookean}
   */
  isCustom: window.STORE.form.isCustom || false
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
  [FORM.UPDATE_FORM_PERMALINK] (state, newValue) {
    if (newValue && newValue !== '') {
      state.permalink = newValue
    }
  },
  [FORM.EMPTY_FORM_FIELDS] (state, status) {
    state.fields = []
  },
  [FORM.REPLACE_FORM_FIELDS] (state, fields) {
    state.fields = fields
  },
  [FORM.UPDATE_FORM_FIELD] (state, field) {
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
  [FORM.REFRESH_FORM_FIELD] (state, field) {
    const fieldIndex = state.fields.findIndex(function (f) {
      return f.name === field.name
    })

    if (fieldIndex !== -1) {
      const fieldToRefresh = state.fields[fieldIndex].value
      state.fields[fieldIndex].value = null
      state.fields[fieldIndex].value = fieldToRefresh
    }
  },
  [FORM.REMOVE_FORM_FIELD] (state, fieldName) {
    state.fields.forEach(function (field, index) {
      if (field.name === fieldName) state.fields.splice(index, 1)
    })
  },
  [FORM.UPDATE_FORM_LOADING] (state, loading) {
    state.loading = loading || !state.loading
  },
  [FORM.SET_FORM_ERRORS] (state, errors) {
    state.errors = errors
  },
  [FORM.CLEAR_FORM_ERRORS] (state) {
    state.errors = []
  },
  [FORM.UPDATE_FORM_SAVE_TYPE] (state, type) {
    state.type = type
  }
}

const actions = {
  [ACTIONS.REPLACE_FORM] ({ commit, state, getters, rootState }, endpoint) {
    return new Promise((resolve, reject) => {
      commit(FORM.CLEAR_FORM_ERRORS)
      commit(NOTIFICATION.CLEAR_NOTIF, 'error')

      api.get(endpoint, function (successResponse) {
        commit(FORM.UPDATE_FORM_LOADING, false)

        let data = successResponse.data

        if (data.hasOwnProperty('languages')) {
          commit(LANGUAGE.REPLACE_LANGUAGES, data.languages)
          delete data.languages
        }

        if (data.hasOwnProperty('revisions')) {
          commit(REVISION.UPDATE_REV_ALL, data.revisions)
          delete data.revisions
        }

        commit(FORM.REPLACE_FORM_FIELDS, data.fields)
        resolve()
      }, function (errorResponse) {
        commit(FORM.UPDATE_FORM_LOADING, false)
        commit(FORM.SET_FORM_ERRORS, errorResponse.response.data)
        reject(errorResponse)
      })
    })
  },
  [ACTIONS.UPDATE_FORM_IN_LISTING] ({ commit, state, getters, rootState }, options) {
    return new Promise((resolve, reject) => {
      commit(FORM.CLEAR_FORM_ERRORS)
      commit(NOTIFICATION.CLEAR_NOTIF, 'error')

      const data = Object.assign(getFormFields(rootState), {
        languages: rootState.language.all
      })

      api[options.method](options.endpoint, data, function (successResponse) {
        commit(FORM.UPDATE_FORM_LOADING, false)

        if (successResponse.data.hasOwnProperty('redirect') && options.redirect) {
          window.location.replace(successResponse.data.redirect)
        }

        commit(NOTIFICATION.SET_NOTIF, { message: successResponse.data.message, variant: successResponse.data.variant })
        resolve()
      }, function (errorResponse) {
        commit(FORM.UPDATE_FORM_LOADING, false)
        commit(FORM.SET_FORM_ERRORS, errorResponse.response.data)
        commit(NOTIFICATION.SET_NOTIF, { message: 'Your submission could not be validated, please fix and retry', variant: 'error' })
        reject(errorResponse)
      })
    })
  },
  [ACTIONS.SAVE_FORM] ({ commit, state, getters, rootState }, saveType) {
    commit(FORM.CLEAR_FORM_ERRORS)
    commit(NOTIFICATION.CLEAR_NOTIF, 'error')

    // update or create etc...
    commit(FORM.UPDATE_FORM_SAVE_TYPE, saveType)

    // we can now create our submitted data object out of:
    // - our just created fields object,
    // - publication properties
    // - selected medias and browsers
    // - created blocks and repeaters
    const data = getFormData(rootState)

    api.put(state.saveUrl, data, function (successResponse) {
      commit(FORM.UPDATE_FORM_LOADING, false)

      if (successResponse.data.hasOwnProperty('redirect')) {
        window.location.replace(successResponse.data.redirect)
      }
      commit(NOTIFICATION.SET_NOTIF, { message: successResponse.data.message, variant: successResponse.data.variant })
      commit(PUBLICATION.UPDATE_PUBLISH_SUBMIT)
      if (successResponse.data.hasOwnProperty('revisions')) {
        commit(REVISION.UPDATE_REV_ALL, successResponse.data.revisions)
      }
    }, function (errorResponse) {
      commit(FORM.UPDATE_FORM_LOADING, false)
      commit(FORM.SET_FORM_ERRORS, errorResponse.response.data)
      commit(NOTIFICATION.SET_NOTIF, { message: 'Your submission could not be validated, please fix and retry', variant: 'error' })
    })
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
